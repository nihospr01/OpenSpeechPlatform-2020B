library ieee;
use ieee.std_logic_1164.all;
use ieee.numeric_std.all;

entity tl_djb_bench is
	port(
		db_clk: in std_logic;
		db_reset: in std_logic;
		db_switches: in std_logic_vector(3 downto 0);
		db_leds: out std_logic_vector(7 downto 0);
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
		spi_cs1: out std_logic;
		--
		--test_seq_idle, test_seq_reset, test_seq_reset_internal: out std_logic;
		test_sync_early, test_sync_late: out std_logic
		--test_sync_2, test_sync: out std_logic
	);
end entity;

architecture a of tl_djb_bench is
	component pll_4_usb is
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
	signal test_sync_early_int, test_sync_late_int: std_logic;
begin
	test_sync_early <= test_sync_early_int;
	test_sync_late <= test_sync_late_int;
	
	db_leds(0) <= db_switches(0) xor db_switches(1) xor db_switches(2) xor db_switches(3);
	db_leds(1) <= not spi_miso;
	db_leds(2) <= not test_sync_early_int;
	db_leds(3) <= not test_sync_late_int;
	db_leds(4) <= db_clk;
	db_leds(5) <= not i2s_dmic;
	db_leds(6) <= '1';
	db_leds(7) <= '0';
	
	int_reset <= not db_reset;

	-- 48 kHz sampling * 2 channels * 32 bits = 3.072 MHz I2S bit clock
	-- LVDS bit clock = 4x I2S bit clock = 12.288 MHz
	-- hr_clk = 4x LVDS bit clock = 49.152 MHz
	-- External clock is 12.000 MHz from USB
	e_pll_4: pll_4_usb port map (
		CLKI => db_clk,
		RST => int_reset,
		CLKOP => hr_clk
	);
	
	e_djb_core: djb_core port map (
		hr_clk => hr_clk, reset => int_reset,
		lvds_io => lvds_io,
		i2s_sclk => i2s_sck, i2s_ws => i2s_ws, i2s_dmic => i2s_dmic, i2s_dspk => i2s_dspk,
		spi_sck => spi_clk, spi_mosi => spi_mosi, spi_miso => spi_miso, spi_cs0 => spi_cs0, spi_cs1 => spi_cs1,
		test_main_clk => open, test_seq_idle => open, test_seq_reset => open,
		test_sync_early => test_sync_early_int, test_sync_late => test_sync_late_int,
		test_seq_reset_internal => open,
		test_sync_2 => open, test_sync => open
	);
	
end architecture;
