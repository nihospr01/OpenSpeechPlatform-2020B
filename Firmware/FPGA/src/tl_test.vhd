library ieee;
use ieee.std_logic_1164.all;
use ieee.numeric_std.all;

entity OSP_DJB_Basic_Test is
	port(
		db_clk: in std_logic;
		db_reset: in std_logic;
		db_switches: in unsigned(3 downto 0);
		db_leds: out unsigned(7 downto 0)
	);
end entity;

architecture a of OSP_DJB_Basic_Test is
	signal counter: unsigned(22 downto 0);
begin
	db_leds(7 downto 4) <= db_switches(3 downto 0);
	process(db_clk, db_reset) is begin
		if db_reset = '0' then
			counter <= (others => '0');
		elsif rising_edge(db_clk) then
			counter <= counter + 1;
		end if;
		db_leds(3 downto 0) <= counter(22 downto 19);
	end process;
end architecture;