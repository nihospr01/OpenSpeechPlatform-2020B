library ieee;
use ieee.std_logic_1164.all;
use ieee.numeric_std.all;

entity tl_djb_field is
	port(
		ext_clk_in: in std_logic;
		codec_clk: out std_logic;
		--
		lvds_io: inout std_logic;
		--
		i2s_sck: out std_logic;
		i2s_ws: out std_logic;
		i2s_dmic: in std_logic;
		i2s_dspk: out std_logic;
		--
		spi_clk: out std_logic;
		spi_mosi: out std_logic;
		spi_miso: in std_logic;
		spi_cs0: out std_logic;
		spi_cs1: out std_logic
		--
	);
end entity;

architecture a of tl_djb_field is
	component pll_4 is
		port(
			CLKI, RST: in std_logic;
			CLKOP: out std_logic
		);
	end component;
	component djb_core is
		port(
			hr_clk, reset: in std_logic;
			--
			lvds_io: inout std_logic;
			--
			i2s_sclk: out std_logic;
			i2s_ws: out std_logic;
			i2s_dmic: in std_logic;
			i2s_dspk: out std_logic;
			--
			spi_sck: out std_logic;
			spi_mosi: out std_logic;
			spi_miso: in std_logic;
			spi_cs0: out std_logic;
			spi_cs1: out std_logic;
			--
			test_main_clk, test_seq_idle, test_seq_reset: out std_logic;
			test_sync_early, test_sync_late: out std_logic;
			test_seq_reset_internal: out std_logic;
			test_sync_2, test_sync: out std_logic
		);
	end component;

	signal hr_clk, int_reset: std_logic;
begin
	codec_clk <= ext_clk_in;
	
	int_reset <= '0';

	-- 48 kHz sampling * 2 channels * 32 bits = 3.072 MHz I2S bit clock
	-- LVDS bit clock = 4x I2S bit clock = 12.288 MHz
	-- hr_clk = 4x LVDS bit clock = 49.152 MHz
	e_pll_4: pll_4 port map (
		CLKI => ext_clk_in,
		RST => int_reset,
		CLKOP => hr_clk
	);
	
	e_djb_core: djb_core port map (
		hr_clk => hr_clk, reset => int_reset,
		lvds_io => lvds_io,
		i2s_sclk => i2s_sck, i2s_ws => i2s_ws, i2s_dmic => i2s_dmic, i2s_dspk => i2s_dspk,
		spi_sck => spi_clk, spi_mosi => spi_mosi, spi_miso => spi_miso, spi_cs0 => spi_cs0, spi_cs1 => spi_cs1,
		test_main_clk => open, test_seq_idle => open, test_seq_reset => open,
		test_sync_early => open, test_sync_late => open,
		test_seq_reset_internal => open,
		test_sync_2 => open, test_sync => open
	);
	
end architecture;
