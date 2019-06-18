library ieee;
use ieee.std_logic_1164.all;
use ieee.numeric_std.all;


-- Assume beginning of LVDS packet occurs at same time as 
-- falling edge of I2S_SCLK where WS also falls (or
-- any multiple of exactly 8 bits later than this time).
-- This means that the first packet actually contains the
-- LSB of the last frame and then the seven MSBs of the
-- current frame, etc.
entity i2s_dio_old is
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
end entity;

architecture a of i2s_dio_old is
	
	component sr8_rising is
		port(
			clk, reset: in std_logic;
			ld, sh: in std_logic;
			pin: in std_logic_vector(7 downto 0);
			pout: out std_logic_vector(7 downto 0);
			sin: in std_logic;
			sout: out std_logic
		);
	end component;
	component sr8_falling is
		port(
			clk, reset: in std_logic;
			ld, sh: in std_logic;
			pin: in std_logic_vector(7 downto 0);
			pout: out std_logic_vector(7 downto 0);
			sin: in std_logic;
			sout: out std_logic
		);
	end component;
	component r8_rising is
		port(
			clk, reset: in std_logic;
			ld: in std_logic;
			din: in std_logic_vector(7 downto 0);
			dout: out std_logic_vector(7 downto 0)
		);
	end component;
	
	signal sclk_ctr: unsigned(1 downto 0);
	signal bit_ctr: unsigned(2 downto 0);
	signal out_1, out_2, out_3: std_logic_vector(7 downto 0);
	signal in_1, in_2, in_3: std_logic_vector(7 downto 0);
	signal lrst_1: std_logic;
	signal cycle8_begin: std_logic;
begin
	-- Clock is MSB of divider
	i2s_sclk <= sclk_ctr(1);
	
	process(main_clk, reset) is begin
		if reset = '1' then
			sclk_ctr <= "00";
			bit_ctr <= "000";
			cycle8_begin <= '0';
			i2s_ws <= '0';
			lrst_1 <= '0';
		elsif rising_edge(main_clk) then
			if seq_reset = '1' then
				-- This happens at the start of bit 2 of a LVDS cycle
				-- which is sclk prescaler 2, bit 0
				sclk_ctr <= "10";
				bit_ctr <= "000";
			else
				if sclk_ctr = "11" then
					-- From 11 to 00: falling edge of sclk, which is when data changes
					if bit_ctr = "111" then
						i2s_ws <= lrst_1;
						lrst_1 <= i2s_lr_st;
					end if;
					bit_ctr <= bit_ctr + 1;
				end if;
				sclk_ctr <= sclk_ctr + 1;
			end if;
		elsif falling_edge(main_clk) then
			cycle8_begin <= '0';
			if sclk_ctr = "11" then
				-- Prepare signals for readiness on rising_edge(main_clk)/falling_edge(sclk)
				if bit_ctr = "111" then
					cycle8_begin <= '1';
				end if;
			end if;
		end if;
	end process;
	
	out_lvds_sr: sr8_rising port map(
		clk => main_clk, reset => reset, ld => '0', sh => i2s_out_sh,
		pin => "00000000", pout => out_1, sin => i2s_out_dat, sout => open
	);
	out_reg_1: r8_rising port map(
		clk => main_clk, reset => reset, ld => i2s_out_ld, din => out_1, dout => out_2
	);
	out_reg_2: r8_rising port map(
		clk => main_clk, reset => reset, ld => i2s_out_ld, din => out_2, dout => out_3
	);
	out_i2s_sr: sr8_falling port map(
		clk => sclk_ctr(1), reset => reset, ld => cycle8_begin, sh => '1',
		pin => out_3, pout => open, sin => '0', sout => i2s_dout
	);
	
	in_i2s_sr: sr8_rising port map(
		clk => sclk_ctr(1), reset => reset, ld => '0', sh => '1',
		pin => "00000000", pout => in_1, sin => i2s_din, sout => open
	);
	in_reg_1: r8_rising port map(
		clk => main_clk, reset => reset, ld => cycle8_begin, din => in_1, dout => in_2
	);
	in_reg_2: r8_rising port map(
		clk => main_clk, reset => reset, ld => cycle8_begin, din => in_2, dout => in_3
	);
	in_lvds_sr: sr8_falling port map(
		clk => main_clk, reset => reset, ld => i2s_in_ld, sh => i2s_in_sh,
		pin => in_3, pout => open, sin => '0', sout => i2s_in_dat
	);
end architecture;
