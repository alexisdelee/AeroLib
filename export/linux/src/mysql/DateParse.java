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
			DateFormat formatter = new SimpleDateFormat("MM-dd-yyyy");
			Date date = (Date)formatter.parse(date_); 
			
			return date.getTime() / 1000L;
		} catch(ParseException e) {
			e.printStackTrace();
		}
		
		return 0;
	}
	
	public Date date(String timestamp) {
		try {
			Timestamp stamp = new Timestamp(Long.parseLong(timestamp, 10) * 1000);
			return new Date(stamp.getTime());
		} catch(NumberFormatException e) {
			e.printStackTrace();
		}
		
		return null;
	}
}
