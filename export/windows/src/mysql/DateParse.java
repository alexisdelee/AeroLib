package mysql;

import java.util.Date;
import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.sql.Timestamp;
import java.util.ArrayList;

public class DateParse {
	public DateParse() {}
	
	public long timestamp(String date_) {
		try {
			DateFormat formatter = new SimpleDateFormat("dd/MM/yyyy");
			Date date = (Date)formatter.parse(date_); 
			
			return date.getTime() / 1000L;
		} catch(ParseException e) {
			e.printStackTrace();
		}
		
		return 0;
	}
	
	public String date(String timestamp) {
		try {			
			DateFormat formatter = new SimpleDateFormat("dd/MM/YYYY HH:mm");
			Timestamp stamp = new Timestamp(Long.parseLong(timestamp, 10) * 1000);
			
			return formatter.format(new Date(stamp.getTime()));
		} catch(NumberFormatException e) {
			e.printStackTrace();
		}
		
		return null;
	}
	
	public long[] monthLimits(String month, String year) {
		long[] limits = new long[2];
		
		int monthLen = this.getNumberOfDays(Integer.parseInt(month), Integer.parseInt(year));
		limits[0] = this.timestamp("01/" + month + "/" + year);
		limits[1] = this.timestamp(monthLen + "/" + month + "/" + year) + 59 + 59 * 60 + 23 * 3600;
		
		return limits;
	}
	
	private int getNumberOfDays(int month, int year) {
		boolean isLeap = ((year % 4) == 0 && ((year % 100) != 0 || (year % 400) == 0));
		ArrayList<Integer> months = new ArrayList<Integer>();
		
		months.add(31);
		
		if(isLeap) months.add(29);
		else months.add(28);
		
		months.add(31);
		months.add(30);
		months.add(31);
		months.add(30);
		months.add(31);
		months.add(31);
		months.add(30);
		months.add(31);
		months.add(30);
		months.add(31);
		
		int monthLen = months.get(month - 1);
		months.clear();
		
		return monthLen;
	}
}
