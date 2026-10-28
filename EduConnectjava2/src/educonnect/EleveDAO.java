package educonnect;

import java.sql.Connection;
import java.sql.Statement;
import java.sql.ResultSet;

public class EleveDAO {

    public static void afficherEleves() {

        try {

            Connection conn = DBConnection.getConnection();

            String sql = "SELECT * FROM eleve";

            Statement st = conn.createStatement();

            ResultSet rs = st.executeQuery(sql);

            System.out.println("\n=== LISTE DES ÉLÈVES ===");

            while(rs.next()) {

                System.out.println(
                    "ID : " + rs.getInt("id_eleve")
                    + " | Nom : " + rs.getString("nom_eleve")
                    + " | Prénom : " + rs.getString("prenom_eleve")
                    + " | Classe : " + rs.getString("classe_eleve")
                );
            }

        } catch(Exception e) {
            e.printStackTrace();
        }
    }
}