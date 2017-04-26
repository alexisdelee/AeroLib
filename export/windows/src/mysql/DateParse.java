package mysql;

import java.util.Date;
import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.sql.Timestamp;

public class DateParse {
	public DateParse() {}
	
	public long timestamp(String date_) {
		try {
			DateFormat formatter = new SimpleDateFormat("dd-MM-yyyy");
			Date date = (Date)formatter.parse(date_); 
			
			return date.getTime() / 1000L;
		} catch(ParseException e) {
			e.printStackTrace();
		}
		
		return 0;
	}
	
	public String date(String timestamp) {
		try {
			DateFormat formatter = DateFormat.getDateTimeInstance(DateFormat.FULL, DateFormat.FULL);
			Timestamp stamp = new Timestamp(Long.parseLong(timestamp, 10) * 1000);
			
			return formatter.format(new Date(stamp.getTime()));
		} catch(NumberFormatException e) {
			e.printStackTrace();
		}
		
		return null;
	}
}
