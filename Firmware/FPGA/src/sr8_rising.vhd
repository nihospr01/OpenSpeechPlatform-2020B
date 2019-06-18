library ieee;
use ieee.std_logic_1164.all;
use ieee.numeric_std.all;

-- 8-bit general purpose shift register (parallel-to-serial and/or serial-to-parallel)
-- Asynchronous reset, rising-edge clk
-- Shifts up (sin => pout(0) => pout(1) etc.)
-- If ld, pin is stored to internal register
-- Else if sh, register is shifted up 1 bit
-- pout is a copy of the internal register state
-- sout is just pout(7)
entity sr8_rising is
	port(
		clk, reset: in std_logic;
		ld, sh: in std_logic;
		pin: in std_logic_vector(7 downto 0);
		pout: out std_logic_vector(7 downto 0);
		sin: in std_logic;
		sout: out std_logic
	);
end entity;

architecture a of sr8_rising is
	signal buf: std_logic_vector(7 downto 0);
begin
	pout <= buf;
	sout <= buf(7);
	process(clk, reset) is begin
		if reset = '1' then
			buf <= (others => '0');
		elsif rising_edge(clk) then
			if ld = '1' then
				buf <= pin;
			elsif sh = '1' then
				buf <= buf(6 downto 0) & sin;
			end if;
		end if;
	end process;
end architecture;