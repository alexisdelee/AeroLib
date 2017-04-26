package mysql;

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
		
		// if(args.length == 1 && Long.valueOf(args[0]).longValue() > 0) {
			PDOUtils bdd = new PDOUtils(url, login, passwd);
			
			try {
				ResultSet rs = bdd.execute("SELECT * FROM user");
				
				while(rs.next()) {
					System.out.println(rs.getString("email"));
				}
				
				DateParse date = new DateParse();
				System.out.println(date.timestamp("03-01-2016"));
				System.out.println(date.date("1451775600"));
				
				Excel xls = new Excel();
				xls.create("C:/Users/Alexis/Documents/ESGI/2016-2017/JAVA/mysql/facture-" + xls.uniqid() + ".xls", "Facture");
				// xls.create("C:/Users/Alexis/Documents/ESGI/2016-2017/JAVA/mysql/facture-" + args[0] + ".xls", "Facture");
			} catch(SQLException e) {
				e.printStackTrace();
			} finally {
				bdd.close_();
			}			
		// } else {
			// System.out.println("Error: error parameter");
		// }
	}
}
