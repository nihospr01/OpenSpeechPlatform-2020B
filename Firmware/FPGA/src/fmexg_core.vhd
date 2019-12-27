library ieee;
use ieee.std_logic_1164.all;
use ieee.numeric_std.all;

entity fmexg_core is
	port(
		clock: in std_logic;
		--
		spi_clk, spi_cs: in std_logic;
		spi_miso: out std_logic;
		adc_clk, adc_pdwn: out std_logic;
		adc_data: in std_logic_vector(11 downto 0);
		interrupt: out std_logic;
		fmexg_mic_sync: in std_logic
	);
end entity;

architecture a of fmexg_core is
	signal counter_div12: unsigned(2 downto 0);
	signal clkdiv12: std_logic;
	
	--signal fifo_readclk: std_logic;
	
	signal spi_bit_ctr: unsigned(2 downto 0);
	signal spi_byte_ctr: unsigned(1 downto 0);
	signal fifo_readclk_en: std_logic;
	signal first_sample: unsigned(11 downto 0);
	signal two_samples: unsigned(23 downto 0);

	--signal fifo_bit: unsigned(3 downto 0);
	--signal hacky_fix: unsigned(1 downto 0);
	--signal spi_data: std_logic;
	
	--signal interrupt_counter: unsigned(7 downto 0); 
	
	signal fifo_Data: std_logic_vector(11 downto 0); 
	signal fifo_WrClock: std_logic; 
	signal fifo_RdClock:  std_logic; 
	signal fifo_WrEn: std_logic; 
	signal fifo_RdEn: std_logic; 
	signal fifo_Reset: std_logic; 
	signal fifo_RPReset: std_logic; 
	signal fifo_Q:  std_logic_vector(11 downto 0);
	signal fifo_Q_u: unsigned(11 downto 0);
	signal fifo_Empty:   std_logic; 
	signal fifo_Full:   std_logic; 
	signal fifo_AlmostEmpty:   std_logic; 
	signal fifo_AlmostFull:   std_logic;
	
	signal rampcounter: unsigned(11 downto 0);
	
component fmexg_fifo_8k_1025 is
	port (
        Data: in  std_logic_vector(11 downto 0); 
        WrClock: in  std_logic; 
        RdClock: in  std_logic; 
        WrEn: in  std_logic; 
        RdEn: in  std_logic; 
        Reset: in  std_logic; 
        RPReset: in  std_logic; 
        Q: out  std_logic_vector(11 downto 0); 
        Empty: out  std_logic; 
        Full: out  std_logic; 
        AlmostEmpty: out  std_logic; 
        AlmostFull: out  std_logic
    );
end component;

begin
	adc_pdwn <= '0';
	
	process(clock) is begin
		if rising_edge(clock) then
			counter_div12 <= counter_div12 + 1;
			if counter_div12 = "101" then
				counter_div12 <= "000";
				clkdiv12 <= not clkdiv12;
			end if;
		end if;
	end process;
	
	adc_clk <= not clkdiv12;
	
	process(clkdiv12) is begin
		if falling_edge(clkdiv12) then
			rampcounter <= rampcounter + 1;
			--interrupt_counter <= interrupt_counter + 1;
		end if;
	end process;
	
	interrupt <= fifo_AlmostFull; --and (and interrupt_counter);
	
	fifo: fmexg_fifo_8k_1025 port map(
		Data => fifo_Data,
        WrClock  => fifo_WrClock,
        RdClock  => fifo_RdClock,
        WrEn  => fifo_WrEn,
        RdEn  => fifo_RdEn, 
        Reset  => fifo_Reset, 
        RPReset  => fifo_RPReset, 
        Q  => fifo_Q,
        Empty  => fifo_Empty,
        Full  => fifo_Full,
        AlmostEmpty  => fifo_AlmostEmpty,
        AlmostFull  => fifo_AlmostFull
	);
	fifo_Q_u <= unsigned(fifo_Q);
	
	fifo_Data <= "100000000000" when fmexg_mic_sync = '1' else adc_data; --std_logic_vector(rampcounter); 
	fifo_WrClock <= clkdiv12;
	fifo_RdClock <= spi_clk;
	fifo_WrEn <= '1';
	fifo_RdEn <= fifo_readclk_en;
	fifo_Reset <= '0';
	fifo_RPReset <= '0';
	
	process(spi_clk) is begin
		if falling_edge(spi_clk) then
			if spi_cs = '0' then
				two_samples <= '0' & two_samples(23 downto 1);
				-- Read new data on second-to-last two rising edges
				if spi_byte_ctr = "10" and (spi_bit_ctr = "110" or spi_bit_ctr = "111") then
					fifo_readclk_en <= '1';
				else
					fifo_readclk_en <= '0';
				end if;
				if spi_byte_ctr = "10" and spi_bit_ctr = "111" then
					--Falling edge of second-to-last clock pulse
					--Register first sample of pair
					first_sample <= unsigned(fifo_Q);
				elsif spi_byte_ctr = "00" and spi_bit_ctr = "000" then
					--Falling edge of last clock pulse
					--Register two samples, swizzle bits
					--VHDL does NOT allow e.g. a(7 downto 0) <= b(0 to 7) to reverse a vector
					two_samples <= fifo_Q_u(4)
								 & fifo_Q_u(5)
								 & fifo_Q_u(6)
								 & fifo_Q_u(7)
								 & fifo_Q_u(8)
								 & fifo_Q_u(9)
								 & fifo_Q_u(10)
								 & fifo_Q_u(11)
								 & first_sample(8)
								 & first_sample(9)
								 & first_sample(10)
								 & first_sample(11)
								 & fifo_Q_u(0)
								 & fifo_Q_u(1)
								 & fifo_Q_u(2)
								 & fifo_Q_u(3)
								 & first_sample(0)
								 & first_sample(1)
								 & first_sample(2)
								 & first_sample(3)
								 & first_sample(4)
								 & first_sample(5)
								 & first_sample(6)
								 & first_sample(7);
				end if;
			end if;
		end if;
	end process;
	
	spi_miso <= two_samples(0);
	
	process(spi_clk, spi_cs) is begin
		if spi_cs = '1' then
			spi_bit_ctr <= "000";
			spi_byte_ctr <= "00";
		elsif rising_edge(spi_clk) then
            --Increment bit (8) and byte (3) counters
            if spi_bit_ctr = "111" then
                if spi_byte_ctr = "10" then
                    spi_byte_ctr <= "00";
                else
                    spi_byte_ctr <= spi_byte_ctr + 1;
                end if;
            end if;
            spi_bit_ctr <= spi_bit_ctr + 1;
		end if;
	end process;
	
end architecture;
