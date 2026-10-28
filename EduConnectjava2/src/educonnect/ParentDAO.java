package educonnect;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;

public class ParentDAO {

    // LOGIN
	public static boolean login(String login, String password) {

	    try {

	        Connection conn = DBConnection.getConnection();

	        String sql = "SELECT * FROM parent WHERE login_compte_parent = ?";

	        PreparedStatement ps = conn.prepareStatement(sql);
	        ps.setString(1, login);

	        ResultSet rs = ps.executeQuery();

	        if (rs.next()) {

	            String hashedPassword = rs.getString("mot_de_passe_parent");

	            return org.mindrot.jbcrypt.BCrypt.checkpw(password, hashedPassword);

	        }

	    } catch (Exception e) {
	        e.printStackTrace();
	    }

	    return false;
	}

    // NOTES ENFANT
    public static void afficherNotes(String login) {

        try {

            Connection conn = DBConnection.getConnection();

            String sql = """
                SELECT e.nom_eleve, m.nom_matiere, n.valeur
                FROM parent p
                JOIN eleve e ON e.id_parent = p.id_parent
                JOIN notes n ON n.id_eleve = e.id_eleve
                JOIN matiere m ON m.id_matiere = n.id_matiere
                WHERE p.login_compte_parent = ?
            """;

            PreparedStatement ps = conn.prepareStatement(sql);
            ps.setString(1, login);

            ResultSet rs = ps.executeQuery();

            System.out.println("\n=== NOTES DE L'ENFANT ===");

            while (rs.next()) {
                System.out.println(
                    rs.getString("nom_eleve")
                    + " | " + rs.getString("nom_matiere")
                    + " | " + rs.getFloat("valeur")
                );
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    // ABSENCES ENFANT
    public static void afficherAbsences(String login) {

        try {

            Connection conn = DBConnection.getConnection();

            String sql = """
                SELECT e.nom_eleve, a.date_absence, a.motif
                FROM parent p
                JOIN eleve e ON e.id_parent = p.id_parent
                JOIN absences a ON a.id_eleve = e.id_eleve
                WHERE p.login_compte_parent = ?
            """;

            PreparedStatement ps = conn.prepareStatement(sql);
            ps.setString(1, login);

            ResultSet rs = ps.executeQuery();

            System.out.println("\n=== ABSENCES DE L'ENFANT ===");

            while (rs.next()) {
                System.out.println(
                    rs.getString("nom_eleve")
                    + " | " + rs.getDate("date_absence")
                    + " | " + rs.getString("motif")
                );
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

//Affichage Bulletin
public static void afficherBulletin(String login) {

    try {

        Connection conn = DBConnection.getConnection();

        // Infos + notes + matières
        String sql = """
            SELECT 
                e.nom_eleve,
                e.prenom_eleve,
                m.nom_matiere,
                n.valeur
            FROM parent p
            JOIN eleve e ON e.id_parent = p.id_parent
            JOIN notes n ON n.id_eleve = e.id_eleve
            JOIN matiere m ON m.id_matiere = n.id_matiere
            WHERE p.login_compte_parent = ?
            ORDER BY e.nom_eleve
        """;

        PreparedStatement ps = conn.prepareStatement(sql);
        ps.setString(1, login);

        ResultSet rs = ps.executeQuery();

        System.out.println("\n=== BULLETIN ===");

        String currentEleve = "";

        while (rs.next()) {

            String nom = rs.getString("nom_eleve") + " " + rs.getString("prenom_eleve");

            if (!nom.equals(currentEleve)) {
                currentEleve = nom;
                System.out.println("\n Élève : " + nom);
            }

            System.out.println(
                "   - " + rs.getString("nom_matiere")
                + " : " + rs.getFloat("valeur")
            );
        }

    } catch (Exception e) {
        e.printStackTrace();
    }
}
public static void afficherMoyenne(String login) {

    try {

        Connection conn = DBConnection.getConnection();

        String sql = """
            SELECT 
                e.nom_eleve,
                e.prenom_eleve,
                SUM(n.valeur * m.coefficient) / SUM(m.coefficient) AS moyenne
            FROM parent p
            JOIN eleve e ON e.id_parent = p.id_parent
            JOIN notes n ON n.id_eleve = e.id_eleve
            JOIN matiere m ON m.id_matiere = n.id_matiere
            WHERE p.login_compte_parent = ?
            GROUP BY e.id_eleve
        """;

        PreparedStatement ps = conn.prepareStatement(sql);
        ps.setString(1, login);

        ResultSet rs = ps.executeQuery();

        System.out.println("\n=== MOYENNES ===");

        while (rs.next()) {

            String nom = rs.getString("nom_eleve") + " " + rs.getString("prenom_eleve");
            double moyenne = rs.getDouble("moyenne");

            String statut;

            if (moyenne >= 12) {
                statut = "🟢 Bon";
            } else if (moyenne >= 8) {
                statut = "🟡 Difficulté";
            } else {
                statut = "🔴 Risque";
            }

            System.out.println(nom + " | Moyenne: " + moyenne + " | " + statut);
        }

    } catch (Exception e) {
        e.printStackTrace();
    }
}
}
