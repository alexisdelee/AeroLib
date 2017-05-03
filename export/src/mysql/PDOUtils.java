package mysql;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

public class PDOUtils {
	private String url_;
	private String login_;
	private String passwd_;
	private Connection cn_;
	private Statement st_;
	
	public PDOUtils(String url, String login, String passwd) {
		this.url_ = url;
		this.login_ = login;
		this.passwd_ = passwd;
		
		this.connect_();
	}
	
	private void connect_() {
		try {
			Class.forName("com.mysql.jdbc.Driver");
			this.cn_ = DriverManager.getConnection(this.url_, this.login_, this.passwd_);
			this.st_ = this.cn_.createStatement();
		} catch(SQLException e) {
			e.printStackTrace();
		} catch(ClassNotFoundException e) {
			e.printStackTrace();
		}
	}

	public void setUrl_(String url_) {
		this.url_ = url_;
	}

	public void setLogin_(String login_) {
		this.login_ = login_;
	}

	public void setPasswd_(String passwd_) {
		this.passwd_ = passwd_;
	}

	public ResultSet execute(String sql) {
		try {
			return this.st_.executeQuery(sql);
		} catch(SQLException e) {
			e.printStackTrace();
		}
		
		return null;
	}
	
	public void close_() {
		try {
			this.cn_.close();
			this.st_.close();
		} catch(SQLException e) {
			e.printStackTrace();
		}
	}
}
