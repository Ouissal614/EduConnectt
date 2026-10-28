package educonnect;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.Statement;

public class NotificationDAO {

    public static void envoyerNotificationsAutomatiques() {

        try {

            Connection conn = DBConnection.getConnection();

            // Calcul moyenne pondérée par élève
            String sql = """
                SELECT 
                    e.id_eleve,
                    e.nom_eleve,
                    e.prenom_eleve,
                    SUM(n.valeur * m.coefficient) / SUM(m.coefficient) AS moyenne
                FROM notes n
                JOIN eleve e ON e.id_eleve = n.id_eleve
                JOIN matiere m ON m.id_matiere = n.id_matiere
                GROUP BY e.id_eleve
            """;

            Statement st = conn.createStatement();
            ResultSet rs = st.executeQuery(sql);

            while(rs.next()) {

                int idEleve = rs.getInt("id_eleve");
                String nom = rs.getString("nom_eleve");
                String prenom = rs.getString("prenom_eleve");
                double moyenne = rs.getDouble("moyenne");

                String message;
                String type;

                if (moyenne >= 12) {
                    message = "Bon travail pour " + nom + " " + prenom;
                    type = "bon";
                } 
                else if (moyenne >= 8) {
                    message = "Attention, difficultés pour " + nom + " " + prenom;
                    type = "difficulte";
                } 
                else {
                    message = "Risque pour " + nom + " " + prenom;
                    type = "risque";
                }

                String insert = "INSERT INTO notification (date_envoi, message, type, lu, id_admin, id_parent) "
                              + "SELECT NOW(), ?, ?, 0, 1, id_parent FROM eleve WHERE id_eleve = ?";

                PreparedStatement ps = conn.prepareStatement(insert);
                ps.setString(1, message);
                ps.setString(2, type);
                ps.setInt(3, idEleve);

                ps.executeUpdate();

                System.out.println("Notification envoyée pour : " + nom + " " + prenom);
            }

        } catch(Exception e) {
            e.printStackTrace();
        }
    }
}