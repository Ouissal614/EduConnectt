package educonnect;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import org.mindrot.jbcrypt.BCrypt;

public class AdminDAO {

    public static boolean login(String email, String password) {

        try {
            Connection conn = DBConnection.getConnection();

            String sql = "SELECT * FROM administrateur WHERE email_admin = ?";

            PreparedStatement ps = conn.prepareStatement(sql);
            ps.setString(1, email);

            ResultSet rs = ps.executeQuery();

            if (rs.next()) {

                String hashedPassword = rs.getString("mot_de_passe_admin");

                return BCrypt.checkpw(password, hashedPassword);
            }

        } catch (Exception e) {
            e.printStackTrace();
        }

        return false;
    }
}