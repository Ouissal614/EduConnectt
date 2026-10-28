package educonnect;

import java.sql.Connection;
import java.sql.Statement;
import java.sql.ResultSet;

public class AbsenceDAO {

    public static void afficherAbsences() {

        try {

            Connection conn = DBConnection.getConnection();

            String sql = """
                SELECT 
                    e.nom_eleve,
                    e.prenom_eleve,
                    a.date_absence,
                    a.motif
                FROM absences a
                JOIN eleve e ON a.id_eleve = e.id_eleve
                ORDER BY a.date_absence DESC
            """;

            Statement st = conn.createStatement();
            ResultSet rs = st.executeQuery(sql);

            System.out.println("\n=== LISTE DES ABSENCES ===");

            while(rs.next()) {

                System.out.println(
                    "Élève : " + rs.getString("nom_eleve") + " " + rs.getString("prenom_eleve")
                    + " | Date : " + rs.getDate("date_absence")
                    + " | Motif : " + rs.getString("motif")
                );
            }

        } catch(Exception e) {
            e.printStackTrace();
        }
    }
}