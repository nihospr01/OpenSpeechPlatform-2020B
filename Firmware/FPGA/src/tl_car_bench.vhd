library ieee;
use ieee.std_logic_1164.all;
use ieee.numeric_std.all;

entity tl_car_bench is
	port(
		db_clk: in std_logic;
		db_reset: in std_logic;
		db_switches: in std_logic_vector(3 downto 0);
		db_leds: out std_logic_vector(7 downto 0);
		--
		l_lvds_io: inout std_logic;
		r_lvds_io: inout std_logic;
		--
		i2s1_sck: out std_logic;
		i2s1_ws: out std_logic;
		i2s1_d0: in std_logic;
		i2s1_d1: in std_logic;
		i2s2_sck: out std_logic;
		i2s2_ws: out std_logic;
		i2s2_d0: out std_logic;
		i2s2_d1: out std_logic;
		--
		spi1_clk: in std_logic;
		spi1_mosi: in std_logic;
		spi1_miso: out std_logic;
		spi1_cs0: in std_logic;
		spi1_cs2: in std_logic;
		spi3_clk: in std_logic;
		spi3_mosi: in std_logic;
		spi3_miso: out std_logic;
		spi3_cs0: in std_logic;
		spi3_cs3: in std_logic;
		--
		test_lrst_pre, test_djb_present: out std_logic
	);
end entity;

architecture a of tl_car_bench is
	component pll_4_usb is
		port(
			CLKI, RST: in std_logic;
			CLKOP: out std_logic
		);
	end component;
	component car_clock_gen is
		port(
			hr_clk, reset: in std_logic;
			main_clk, seq_reset, i2s_ws: out std_logic
		);
	end component;
	component car_core is
		port(
			main_clk, reset, seq_reset, i2s_ws: in std_logic;
			--
			lvds_io: inout std_logic;
			--
			i2s_sclk: out std_logic;
			i2s_dspk: in std_logic;
			i2s_dmic: out std_logic;
			--
			spi_sck: in std_logic;
			spi_mosi: in std_logic;
			spi_miso: out std_logic;
			spi_cs0: in std_logic;
			spi_cs1: in std_logic;
			--
			test_lrst_pre, test_djb_present: out std_logic
		);
	end component;

	signal hr_clk, main_clk, int_reset, seq_reset: std_logic;
	signal i2s_sck, i2s_ws: std_logic;
begin
	db_leds(3 downto 0) <= db_switches;
	db_leds(4) <= db_clk;
	db_leds(5) <= i2s1_d1;
	db_leds(6) <= '0';
	db_leds(7) <= '1';
	
	int_reset <= not db_reset;

	i2s1_sck <= i2s_sck;
	i2s2_sck <= i2s_sck;
	i2s1_ws <= i2s_ws;
	i2s2_ws <= i2s_ws;

	-- 48 kHz sampling * 2 channels * 32 bits = 3.072 MHz I2S bit clock
	-- LVDS bit clock = 4x I2S bit clock = 12.288 MHz
	-- hr_clk = 4x LVDS bit clock = 49.152 MHz
	-- External clock is 12.000 MHz from USB
	e_pll_4: pll_4_usb port map (
		CLKI => db_clk,
		RST => int_reset,
		CLKOP => hr_clk
	);
	e_car_clock_gen: car_clock_gen port map (
		hr_clk => hr_clk, reset => int_reset,
		main_clk => main_clk, seq_reset => seq_reset, i2s_ws => i2s_ws
	);
	
	l_car_core: car_core port map (
		main_clk => main_clk, reset => int_reset, seq_reset => seq_reset, i2s_ws => i2s_ws,
		lvds_io => l_lvds_io,
		i2s_sclk => i2s_sck, i2s_dmic => i2s2_d0, i2s_dspk => i2s1_d0,
		spi_sck => spi1_clk,
		spi_mosi => spi1_mosi,
		spi_miso => spi1_miso,
		spi_cs0 => spi1_cs0,
		spi_cs1 => spi1_cs2,
		test_lrst_pre => open, test_djb_present => open
	);
	
	r_car_core: car_core port map (
		main_clk => main_clk, reset => int_reset, seq_reset => seq_reset, i2s_ws => i2s_ws,
		lvds_io => r_lvds_io,
		i2s_sclk => open, i2s_dmic => i2s2_d1, i2s_dspk => i2s1_d0,
		spi_sck => spi3_clk,
		spi_mosi => spi3_mosi,
		spi_miso => spi3_miso,
		spi_cs0 => spi3_cs0,
		spi_cs1 => spi3_cs3,
		test_lrst_pre => test_lrst_pre, test_djb_present => test_djb_present
	);
	
end architecture;
