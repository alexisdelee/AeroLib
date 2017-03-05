package mysql;

import java.io.IOException;
import java.io.PrintWriter;
import java.sql.ResultSet;
import java.sql.SQLException;

public class Main {
	public static void main(String[] args) {		
		// String url = "jdbc:mysql://192.168.10.139:3306/aerodrome";
		// String login = "staff";
		// String passwd = "staff";
		String url = "jdbc:mysql://localhost/aerodrome";
		String login = "root";
		String passwd = "";
		
		LogBDD bdd = new LogBDD(url, login, passwd);
		
		try {
			ResultSet rs = bdd.execute("SELECT * FROM user");
			
			PrintWriter writer = new PrintWriter("test.txt", "utf-8");
			while(rs.next()) {
				System.out.println(rs.getString("email"));
				writer.println(rs.getString("email"));
			}
			writer.close();
		} catch(SQLException e) {
			e.printStackTrace();
		} catch(IOException e) {
			e.printStackTrace();
		} finally {
			bdd.close_();
		}
		
		DateParse date = new DateParse();
		System.out.println(date.timestamp("03-01-2016"));
		System.out.println(date.date("1456786800"));
	}
}
