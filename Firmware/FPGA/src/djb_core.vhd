library ieee;
use ieee.std_logic_1164.all;
use ieee.numeric_std.all;

entity djb_core is
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
end entity;

architecture a of djb_core is
	component djb_clock_sync is
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
	end component;
	component djb_sequencer is
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
	
	signal main_clk: std_logic;
	signal seq_idle, seq_reset: std_logic;
	signal i2s_spkr_dat, i2s_spkr_sh, i2s_spkr_ld: std_logic;
	signal i2s_mic_ld, i2s_mic_sh, i2s_mic_dat: std_logic;
	signal i2s_lr_st: std_logic;
	signal spi_cs0_n, spi_cs1_n: std_logic;
begin
	test_main_clk <= main_clk;
	test_seq_idle <= seq_idle; test_seq_reset <= seq_reset;
	spi_cs0 <= not spi_cs0_n; spi_cs1 <= not spi_cs1_n;
	
	e_djb_clock_sync: djb_clock_sync port map (
		hr_clk => hr_clk, reset => reset,
		seq_idle => seq_idle,
		lvds_i => lvds_io,
		main_clk => main_clk,
		seq_reset => seq_reset,
		sync_early => test_sync_early, sync_late => test_sync_late,
		test_seq_reset_internal => test_seq_reset_internal,
		test_sync_2=> test_sync_2, test_sync => test_sync
	);
	e_djb_sequencer: djb_sequencer port map (
		main_clk => main_clk, 
		reset => reset,
		seq_reset => seq_reset,
		seq_idle => seq_idle,
		--
		lvds_io => lvds_io,
		--
		i2s_spkr_dat => i2s_spkr_dat,
		i2s_spkr_sh => i2s_spkr_sh,
		i2s_spkr_ld => i2s_spkr_ld,
		i2s_mic_ld => i2s_mic_ld,
		i2s_mic_sh => i2s_mic_sh,
		i2s_mic_dat => i2s_mic_dat,
		i2s_lr_st => i2s_lr_st,
		--
		spi_sck => spi_sck, 
		spi_mosi => spi_mosi, 
		spi_cs0_n => spi_cs0_n, 
		spi_cs1_n => spi_cs1_n,
		spi_miso => spi_miso
	);
	e_i2s_dio_2: i2s_dio_2 port map (
		main_clk => main_clk, reset => reset, seq_reset => seq_reset,
		i2s_out_dat => i2s_spkr_dat, i2s_out_sh => i2s_spkr_sh, i2s_out_ld => i2s_spkr_ld,
		i2s_in_ld => i2s_mic_ld, i2s_in_sh => i2s_mic_sh, i2s_in_dat => i2s_mic_dat,
		i2s_lr_st => i2s_lr_st,
		i2s_sclk => i2s_sclk, i2s_ws => i2s_ws, i2s_din => i2s_dmic, i2s_dout => i2s_dspk
	);
end architecture;
