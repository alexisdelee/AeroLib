package mysql;

import java.io.FileOutputStream;
import org.apache.poi.hssf.usermodel.HSSFSheet;
import org.apache.poi.hssf.usermodel.HSSFWorkbook;
import org.apache.poi.hssf.usermodel.HSSFRow;
import org.apache.poi.hssf.usermodel.HSSFCell;
import java.nio.ByteBuffer;
import java.security.SecureRandom;
import java.util.zip.CRC32;
import java.util.UUID;

public class Excel {	
	public Excel() {}
	
	public void create(String filename, String title) {
		try {
			HSSFWorkbook workbook = new HSSFWorkbook();
			HSSFSheet sheet = workbook.createSheet(title);
			
			HSSFRow rowhead = sheet.createRow((short)0);
			rowhead.createCell(0).setCellValue("No.");
			
			HSSFRow row = sheet.createRow((short)1);
			row.createCell(0).setCellValue("1");
			
			FileOutputStream fileExport = new FileOutputStream(filename);
			workbook.write(fileExport);
			fileExport.close();
			System.out.println("Your excel file has been generated!");
		} catch(Exception ex) {
			System.out.println(ex);
		}
	}
	
	public String uniqid() {
		String suuid = UUID.randomUUID().toString();
		
		CRC32 crc = new CRC32();
		crc.update(suuid.getBytes());
		return Long.toHexString(crc.getValue());
	}
}
