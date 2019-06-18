library ieee;
use ieee.std_logic_1164.all;
use ieee.numeric_std.all;

-- Create and synchronize the main internal clock (LVDS bit clock) with the
-- rising edge between bits 0 and 1 of the LVDS packet.
entity car_clock_gen is
	port(
		hr_clk, reset: in std_logic;
		main_clk, seq_reset, i2s_ws: out std_logic
	);
end entity;

architecture a of car_clock_gen is
	signal divider: unsigned(1 downto 0);
	signal lvds_ctr: unsigned(7 downto 0);
begin
	process(hr_clk, reset) is begin
		if reset = '1' then
			-- Initialize internal signals
			divider <= (others => '1');
			lvds_ctr <= (others => '1');
		elsif rising_edge(hr_clk) then
			if divider = "11" then
				lvds_ctr <= lvds_ctr + 1;
			end if;
			divider <= divider + 1;
		end if;
	end process;
	-- Inverted divide by 4 counter
	main_clk <= not divider(1);
	-- I2S spec has left channel LRCLK be low
	i2s_ws <= lvds_ctr(7);
	
	--Create virtual seq_reset
	process(reset, divider(1)) is begin
		if reset = '1' then
			seq_reset <= '0';
		elsif rising_edge(divider(1)) then --falling_edge(main_clk)
			-- seq_reset should be high during rising_edge(main_clk)
			-- of bit 2 out of every LVDS packet (32 main clocks)
			if lvds_ctr(4 downto 0) = "00001" then
				seq_reset <= '1';
			else
				seq_reset <= '0';
			end if;
		end if;
	end process;
end architecture;