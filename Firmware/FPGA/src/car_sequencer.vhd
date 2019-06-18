library ieee;
use ieee.std_logic_1164.all;
use ieee.numeric_std.all;

entity car_sequencer is
	port(
		main_clk, reset: in std_logic;
		i2s_lr_st: in std_logic;
		--
		lvds_io: inout std_logic;
		--
		i2s_spkr_dat: in std_logic;
		i2s_spkr_sh, i2s_spkr_ld: out std_logic;
		i2s_mic_ld, i2s_mic_sh, i2s_mic_dat: out std_logic;
		--
		spi_sck, spi_mosi, spi_cs0_n, spi_cs1_n: in std_logic;
		spi_miso: out std_logic;
		--
		mute_sync: in std_logic;
		--
		test_djb_present: out std_logic
	);
end entity;

architecture a of car_sequencer is
	signal lvdscounter: unsigned(4 downto 0);
	signal djb_present: std_logic;
	signal djb_got16: std_logic;
begin
	test_djb_present <= djb_present;

	process(main_clk, reset) is begin
		if reset = '1' then
			-- Initialize signals
			lvdscounter <= (others => '1');
			djb_present <= '0';
			djb_got16 <= '1';
			--
			lvds_io <= '0';
			--
			i2s_spkr_sh <= '0';
			i2s_spkr_ld <= '0';
			i2s_mic_ld <= '0';
			i2s_mic_sh <= '0';
			i2s_mic_dat <= '0';
			--
			spi_miso <= '0';
		elsif falling_edge(main_clk) then
			-- Capture data on falling edge
			-- The following signals will be in registers triggered on falling edge:
			i2s_mic_ld <= '0';
			i2s_mic_sh <= '0';
			i2s_mic_dat <= '0';
			-- Also spi_miso which keeps its last state
			-- Bits of LVDS packet
			case to_integer(lvdscounter) is
				when 16 => 
					djb_got16 <= lvds_io;
				when 17 => 
					-- Make sure DJB responded with 0-1 transition
					if djb_got16 = '0' and lvds_io = '1' then
						djb_present <= '1';
					else
						djb_present <= '0';
					end if;
				when 18 | 19 | 20 | 21 | 22 | 23 | 24 | 25 =>
					if djb_present = '1' then
						i2s_mic_dat <= '0' when mute_sync = '1' else lvds_io;
						i2s_mic_sh <= '1';
					end if;
				when 26 =>
					if djb_present = '1' then
						i2s_mic_ld <= '1';
						spi_miso <= lvds_io;
					else
						spi_miso <= '0';
					end if;
				when others =>
					null; --handled below
			end case;
		elsif rising_edge(main_clk) then
			-- Signal default values:
			i2s_spkr_ld <= '0';
			i2s_spkr_sh <= '0';
			-- State change on rising edge
			if djb_present = '1' then
				-- Normal mode
				case to_integer(lvdscounter)+1 is --Plus 1 because this is looking at the last value of the counter
					when 0 | 32 =>
						lvds_io <= '0';
					when 1 =>
						lvds_io <= '1';
						i2s_spkr_ld <= '1';
					when 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 =>
						lvds_io <= i2s_spkr_dat;
						i2s_spkr_sh <= '1'; --Should actually be '0' for 9, but doesn't matter if we shift an extra 0 around
					when 10 =>
						lvds_io <= i2s_lr_st;
					when 11 =>
						lvds_io <= spi_sck;
					when 12 => 
						lvds_io <= spi_mosi;
					when 13 =>
						lvds_io <= spi_cs0_n;
					when 14 =>
						lvds_io <= spi_cs1_n;
					when 15 | 16 | 17 | 18 | 19 | 20 | 21 | 22 | 23 | 24 | 25 | 26 | 27 =>
						lvds_io <= 'Z';
					when others =>
						lvds_io <= '0';
				end case;
			else
				-- If DJB not connected, send all normally-driven data as 0s, so the only
				-- possible rising edge is the correct one at the beginning
				case to_integer(lvdscounter)+1 is --Plus 1 because this is looking at the last value of the counter
					when 0 | 32 =>
						lvds_io <= '0';
					when 1 =>
						lvds_io <= '1';
					when 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | 11 | 12 | 13 | 14 =>
						lvds_io <= '0';
					when 15 | 16 | 17 | 18 | 19 | 20 | 21 | 22 | 23 | 24 | 25 | 26 | 27 =>
						lvds_io <= 'Z';
					when others =>
						lvds_io <= '0';
				end case;
			end if;
			lvdscounter <= lvdscounter + 1;
		end if;
	end process;
end architecture;
