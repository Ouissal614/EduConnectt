package educonnect;

import java.sql.Connection;
import java.sql.DriverManager;

public class DBConnection {

    public static Connection getConnection() {

        Connection conn = null;

        try {
            String url = "jdbc:mysql://localhost:3306/projet_synthese";
            String user = "root";
            String password = "MAN@chaehei14";

            conn = DriverManager.getConnection(url, user, password);

            System.out.println("Connexion MySQL réussie ✔");

        } catch (Exception e) {
            System.out.println("Erreur connexion : " + e.getMessage());
        }

        return conn;
    }
}