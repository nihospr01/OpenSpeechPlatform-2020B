library ieee;
use ieee.std_logic_1164.all;
use ieee.numeric_std.all;

-- 8-bit register, rising-edge triggered.
entity r8_rising is
	port(
		clk, reset: in std_logic;
		ld: in std_logic;
		din: in std_logic_vector(7 downto 0);
		dout: out std_logic_vector(7 downto 0)
	);
end entity;

architecture a of r8_rising is
	signal reg: std_logic_vector(7 downto 0);
begin
	process(clk, reset) is begin
		if reset = '1' then
			reg <= "00000000";
		elsif rising_edge(clk) then
			if ld = '1' then
				reg <= din;
			end if;
		end if;
	end process;
	dout <= reg;
end architecture;