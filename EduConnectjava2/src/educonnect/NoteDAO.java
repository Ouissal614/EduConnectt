package educonnect;

import java.sql.Connection;
import java.sql.Statement;
import java.sql.ResultSet;

public class NoteDAO {

    public static void afficherNotes() {

        try {

            Connection conn = DBConnection.getConnection();

            String sql = """
                SELECT 
                    e.nom_eleve,
                    m.nom_matiere,
                    n.valeur,
                    n.date_evaluation
                FROM notes n
                JOIN eleve e ON n.id_eleve = e.id_eleve
                JOIN matiere m ON n.id_matiere = m.id_matiere
            """;

            Statement st = conn.createStatement();
            ResultSet rs = st.executeQuery(sql);

            System.out.println("\n=== LISTE DES NOTES ===");

            while(rs.next()) {

                System.out.println(
                    "Élève : " + rs.getString("nom_eleve")
                    + " | Matière : " + rs.getString("nom_matiere")
                    + " | Note : " + rs.getFloat("valeur")
                    + " | Date : " + rs.getDate("date_evaluation")
                );
            }

        } catch(Exception e) {
            e.printStackTrace();
        }
    }
}