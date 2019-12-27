library ieee;
use ieee.std_logic_1164.all;
use ieee.numeric_std.all;

entity car_core is
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
		fmexg_mic_sync: in std_logic;
		muted: in std_logic;
		--
		test_lrst_pre, test_djb_present: out std_logic
	);
end entity;

architecture a of car_core is
	component car_sequencer is
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
			fmexg_mic_sync: in std_logic;
			muted: in std_logic;
			--
			test_djb_present: out std_logic
		);
	end component;
	component i2s_dio_2 is
		port(
			main_clk, reset, seq_reset: in std_logic;
			--
			i2s_out_dat, i2s_out_sh, i2s_out_ld: in std_logic;
			i2s_in_ld, i2s_in_sh: in std_logic;
			i2s_in_dat: out std_logic;
			i2s_lr_st: in std_logic;
			--
			i2s_sclk: out std_logic;
			i2s_ws: out std_logic;
			i2s_din: in std_logic;
			i2s_dout: out std_logic
		);
	end component;
	
	---------------------------------------------------------------------------
	
	signal i2s_spkr_dat, i2s_spkr_sh, i2s_spkr_ld: std_logic;
	signal i2s_mic_ld, i2s_mic_sh, i2s_mic_dat: std_logic;
	signal i2s_lr_st_pre, i2s_lr_st: std_logic;
	signal spi_cs0_n, spi_cs1_n: std_logic;
	signal spi_miso_internal: std_logic;
begin
	spi_cs0_n <= not spi_cs0;
	spi_cs1_n <= not spi_cs1;
	spi_miso <= spi_miso_internal when (spi_cs0 and spi_cs1) = '0' else 'Z';
	
	test_lrst_pre <= i2s_lr_st;
	
	-- Sample i2s_ws_internal to avoid race condition, it changes exactly when
	-- it's read by i2s_dio (rising edge of main_clk of bit 0)
	process(reset, main_clk) is begin
		if reset = '1' then
			i2s_lr_st_pre <= '0';
		elsif falling_edge(main_clk) then
			i2s_lr_st_pre <= i2s_ws;
		end if;
	end process;
	
	e_car_sequencer: car_sequencer port map (
		main_clk => main_clk, 
		reset => reset,
		i2s_lr_st => i2s_lr_st,
		--
		lvds_io => lvds_io,
		--
		i2s_mic_dat => i2s_mic_dat,
		i2s_mic_sh => i2s_mic_sh,
		i2s_mic_ld => i2s_mic_ld,
		i2s_spkr_ld => i2s_spkr_ld,
		i2s_spkr_sh => i2s_spkr_sh,
		i2s_spkr_dat => i2s_spkr_dat,
		--
		spi_sck => spi_sck, 
		spi_mosi => spi_mosi, 
		spi_cs0_n => spi_cs0_n, 
		spi_cs1_n => spi_cs1_n,
		spi_miso => spi_miso_internal,
		--
		fmexg_mic_sync => fmexg_mic_sync,
		muted => muted,
		--
		test_djb_present => test_djb_present
	);
	e_i2s_dio: i2s_dio_2 port map (
		main_clk => main_clk, reset => reset, seq_reset => seq_reset,
		i2s_out_dat => i2s_mic_dat, i2s_out_sh => i2s_mic_sh, i2s_out_ld => i2s_mic_ld,
		i2s_in_ld => i2s_spkr_ld, i2s_in_sh => i2s_spkr_sh, i2s_in_dat => i2s_spkr_dat,
		i2s_lr_st => i2s_lr_st_pre,
		i2s_sclk => i2s_sclk, i2s_ws => i2s_lr_st, i2s_din => i2s_dspk, i2s_dout => i2s_dmic
	);
end architecture;
