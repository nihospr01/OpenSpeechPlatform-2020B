library ieee;
use ieee.std_logic_1164.all;
use ieee.numeric_std.all;

-- Create and synchronize the main internal clock (LVDS bit clock) with the
-- rising edge between bits 0 and 1 of the LVDS packet.
entity djb_clock_sync is
	port(
		hr_clk, reset: in std_logic;
		seq_idle: in std_logic;
		lvds_i: in std_logic;
		main_clk: out std_logic;
		seq_reset: out std_logic;
		sync_early, sync_late: out std_logic;
		--
		test_seq_reset_internal: out std_logic;
		test_sync_2, test_sync: out std_logic
	);
end entity;

architecture a of djb_clock_sync is
	signal divider: unsigned(1 downto 0);
	signal seq_reset_i: std_logic;
	signal lvds_i_sampled, last_lvds_i: std_logic;
begin
	test_seq_reset_internal <= seq_reset_i;
	seq_reset <= seq_reset_i;
	
	test_sync <= '0';
	test_sync_2 <= '0';
	
	process(hr_clk, reset) is begin
		if reset = '1' then
			lvds_i_sampled <= '0';
		elsif rising_edge(hr_clk) then
			lvds_i_sampled <= lvds_i;
		end if;
	end process;
	
	process(hr_clk, reset) is begin
		if reset = '1' then
			-- Initialize internal signals
			divider <= (others => '1');
			last_lvds_i <= '0';
			seq_reset_i <= '0';
			sync_early <= '0';
			sync_late <= '0';
		elsif rising_edge(hr_clk) then
			--Signal initial values
			sync_early <= '0';
			sync_late <= '0';
			--
			if seq_idle = '1' and seq_reset_i = '0' and last_lvds_i = '0' and lvds_i_sampled = '1' then
				-- Reset sequencer, this will cause the sequencer to disable seq_idle
				seq_reset_i <= '1';
				divider <= "10";
				-- Do we need to synchronize?
				case divider is
					when "01" =>
						-- If the LVDS edge is perfectly in sync, divider will be 01
						-- and about to go to 10 (because lvds_i has  been sampled, delayed 1 clock, plus extra 1  sample delay for no reason)
						null;
					when "00" =>
						-- Sync edge happened slightly early
						sync_early <= '1';
					when "10" =>
						-- Sync edge happened slightly late
						sync_late <= '1';
					when others =>
						-- Bad
						sync_early <= '1';
						sync_late <= '1';
				end case;
			else
				-- Turn off reset if the sequencer is no longer idle 
				if divider = "01" and seq_idle = '0' then --Going to be falling edge of main_clk
					seq_reset_i <= '0';
				end if;
				-- Increment clock
				divider <= divider + 1;
			end if;
			-- Store last data
			last_lvds_i <= lvds_i_sampled;
		end if;
	end process;
	
	-- Inverted divide by 4 counter
	main_clk <= not divider(1);
	
end architecture;