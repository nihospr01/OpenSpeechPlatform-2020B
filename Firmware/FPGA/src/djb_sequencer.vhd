library ieee;
use ieee.std_logic_1164.all;
use ieee.numeric_std.all;

entity djb_sequencer is
	port(
		main_clk, reset: in std_logic;
		seq_reset: in std_logic;
		seq_idle: out std_logic;
		--
		lvds_io: inout std_logic;
		--
		i2s_spkr_dat, i2s_spkr_sh, i2s_spkr_ld: out std_logic;
		i2s_mic_ld, i2s_mic_sh: out std_logic;
		i2s_mic_dat: in std_logic;
		i2s_lr_st: out std_logic;
		--
		spi_sck, spi_mosi, spi_cs0_n, spi_cs1_n: out std_logic;
		spi_miso: in std_logic
	);
end entity;

architecture a of djb_sequencer is
	signal lvdscounter: unsigned(4 downto 0);
	signal seq_idle_internal: std_logic;
begin
	-- Signal copy
	seq_idle <= seq_idle_internal;
	
	process(main_clk, reset) is begin
		if reset = '1' then
			-- Initialize signals
			lvdscounter <= (others => '1');
			seq_idle_internal <= '1';
			--
			lvds_io <= 'Z';
			--
			i2s_spkr_dat <= '0';
			i2s_spkr_sh <= '0';
			i2s_spkr_ld <= '0';
			i2s_mic_ld <= '0';
			i2s_mic_sh <= '0';
			i2s_lr_st <= '0';
			--
			spi_sck <= '0';
			spi_mosi <= '0';
			spi_cs0_n <= '0';
			spi_cs1_n <= '0';
		elsif falling_edge(main_clk) then
			-- Capture data on falling edge
			-- The following signals will be in registers triggered on falling edge:
			i2s_spkr_dat <= '0';
			i2s_spkr_sh <= '0';
			i2s_spkr_ld <= '0';
			-- Also spi_sck, spi_mosi, spi_cs0_n, spi_cs1_n which keep their last states
			-- Bits of LVDS packet
			case to_integer(lvdscounter) is
				when 0 => 
					null; --lvds_io driven 0 by carrier
				when 1 => 
					null; --lvds_io driven 1 by carrier
				when 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 =>
					i2s_spkr_dat <= lvds_io;
					i2s_spkr_sh <= '1';
				when 10 =>
					i2s_spkr_ld <= '1';
					i2s_lr_st <= lvds_io;
				when 11 =>
					spi_sck <= lvds_io;
				when 12 =>
					spi_mosi <= lvds_io;
				when 13 =>
					spi_cs0_n <= lvds_io;
				when 14 =>
					spi_cs1_n <= lvds_io;
				when 15 => 
					null; --lvds_io released by carrier
				when others =>
					null; --handled below
			end case;
		elsif rising_edge(main_clk) then
			-- Signal default values:
			lvds_io <= 'Z';
			i2s_mic_ld <= '0';
			i2s_mic_sh <= '0';
			-- State change on rising edge
			if seq_reset = '1' then
				lvdscounter <= "00010"; --This happens at the start of bit 2
				seq_idle_internal <= '0';
			else
				case to_integer(lvdscounter)+1 is --Plus 1 because this is looking at the last value of the counter
					when 16 =>
						lvds_io <= '0';
					when 17 =>
						lvds_io <= '1';
						i2s_mic_ld <= '1';
					when 18 | 19 | 20 | 21 | 22 | 23 | 24 | 25 =>
						lvds_io <= i2s_mic_dat;
						i2s_mic_sh <= '1'; --Should actually be '0' for 25, but doesn't matter if we shift an extra 0 around
					when 26 =>
						lvds_io <= spi_miso;
					when 27 | 28 | 29 | 30 | 31 =>
						seq_idle_internal <= '1'; --lvds_io released by DJB
					when others =>
						null; --handled above
				end case;
				if seq_idle_internal = '0' then
					lvdscounter <= lvdscounter + 1;
				end if;
			end if;
		end if;
	end process;
end architecture;
