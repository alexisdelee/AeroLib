package mysql;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.regex.*;

public class Main {
	public static void main(String[] args) {		
		// String url = "jdbc:mysql://192.168.10.141:3306/aerodrome";
		// String login = "staff";
		// String passwd = "staff";
		String url = "jdbc:mysql://localhost/aerodrome";
		String login = "root";
		String passwd = "";
		
		if(args.length == 2) {
			Pattern pattern = Pattern.compile("^0[1-9]|1[0-2]$");
			Matcher matcher = pattern.matcher(args[0]);
			boolean result = matcher.matches();
			
			if(!result) {
				System.out.println("Exception: the month must be between 01 and 12");
				System.exit(666);
			}
			
			pattern = Pattern.compile("^19[7-9][0-9]|20[0-9]{2}|2100$");
			matcher = pattern.matcher(args[1]);
			result = matcher.matches();
			
			if(!result) {
				System.out.println("Exception: the year must be between 1970 and 2100");
				System.exit(666);
			}
			
			Excel xls = new Excel("Facture");
			PDOUtils bdd = new PDOUtils(url, login, passwd);
			DateParse date = new DateParse();
			
			long[] limits = date.monthLimits("04", "2017");
			
			try {
				ResultSet rs = bdd.execute("SELECT user.name, service.description, service.subscription, service.dateStart, service.dateEnd, service.idAeroclub, service.costService, service.tvaService FROM `receipt` "
						+ "LEFT JOIN `service` ON receipt.idReceipt = service.idReceipt "
						+ "LEFT JOIN `user` ON receipt.idUser = user.idUser "
						+ "WHERE service.description IS NOT NULL "
						+ "AND user.statut = 1 "
						+ "AND service.subscription >= " + String.valueOf(limits[0]) + " "
						+ "AND service.subscription <= " + String.valueOf(limits[1]) + " "
						+ "GROUP BY service.idService");
				
				String[] head = {"Nom", "Activité", "Inscription", "Date de début", "Date de fin", "Lié à l'aéroclub", "HT", "TVA", "TTC"};
				xls.header(head);
				
				while(rs.next()) {
					String[] data = {
						rs.getString("name"),
						rs.getString("description"),
						date.date(String.valueOf(rs.getLong("subscription"))),
						date.date(String.valueOf(rs.getLong("dateStart"))),
						date.date(String.valueOf(rs.getLong("dateEnd"))),
						((Integer)rs.getObject("idAeroclub") == null ? "non" : "oui"),
						String.valueOf(rs.getDouble("costService")),
						String.valueOf(rs.getDouble("tvaService")),
						String.format("%.2f", rs.getDouble("costService") + rs.getDouble("tvaService"))
					};
					xls.append(data);
				}
				
				xls.generate("facture-" + xls.uniqid() + ".xls");
				
				System.out.println(xls.getFilename());
			} catch(SQLException e) {
				e.printStackTrace();
			} finally {
				bdd.close_();
			}
		} else {
			System.out.println("Exception: error parameter");
		}
	}
}
