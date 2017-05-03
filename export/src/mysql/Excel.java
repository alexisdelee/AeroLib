package mysql;

import java.io.FileOutputStream;
import org.apache.poi.hssf.usermodel.HSSFSheet;
import org.apache.poi.hssf.usermodel.HSSFWorkbook;
import org.apache.poi.hssf.usermodel.HSSFRow;
import org.apache.poi.hssf.usermodel.HSSFCell;
import java.util.zip.CRC32;
import java.util.UUID;

public class Excel {
	private HSSFWorkbook workbook_;
	private HSSFSheet sheet_;
	private int cursor_;
	private String filename_;
	
	public Excel(String title) {
		this.workbook_ = new HSSFWorkbook();
		this.sheet_ = this.workbook_.createSheet(title);
		this.cursor_ = 1;
		this.filename_ = null;
	}
	
	public void header(String[] head) {
		HSSFRow row = this.sheet_.createRow(0);
		HSSFCell cell = null;
		
		for(int h = 0, _length = head.length; h < _length; h++) {			
			cell = row.createCell((short)h);
			cell.setCellValue(head[h]);
			this.sheet_.autoSizeColumn(h);
		}
	}
	
	public void append(String[] data) {
		HSSFRow row = this.sheet_.createRow(this.cursor_);
		HSSFCell cell = null;
		
		for(int d = 0, _length = data.length; d < _length; d++) {			
			cell = row.createCell((short)d);
			cell.setCellValue(data[d]);
			
			this.sheet_.autoSizeColumn(d);
		}
		
		this.cursor_++;
	}
	
	public void generate(String filename) {
		this.filename_ = filename;
		
		try {
			FileOutputStream fileExport = new FileOutputStream("../account/" + filename);
			this.workbook_.write(fileExport);
			fileExport.close();
			this.workbook_.close();
		} catch(Exception e) {
			System.out.println(e);
		}
	}
	
	public String uniqid() {
		String suuid = UUID.randomUUID().toString();
		
		CRC32 crc = new CRC32();
		crc.update(suuid.getBytes());
		return Long.toHexString(crc.getValue());
	}
	
	public String getPathname() {
		return "account/" + this.filename_;
	}
}
