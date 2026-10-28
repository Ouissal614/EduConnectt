package educonnect;

import java.util.Scanner;

public class TestLoginAdmin {

    public static void main(String[] args) {

        Scanner sc = new Scanner(System.in);

        while (true) {

            //  LOGIN
            System.out.println("\n=== LOGIN ADMIN ===");

            System.out.print("Email : ");
            String email = sc.nextLine();

            System.out.print("Mot de passe : ");
            String password = sc.nextLine();

            boolean ok = AdminDAO.login(email, password);

            if (ok) {

                System.out.println("Connexion réussie ✔ Admin connecté");

                int choix = 0;

                //  MENU LOOP
                while (choix != 5) {

                    System.out.println("\n=== MENU ADMIN ===");
                    System.out.println("1. Affichage des élèves");
                    System.out.println("2. Affichage des notes");
                    System.out.println("3. Affichage des absences");
                    System.out.println("4. Envoyer notification");
                    System.out.println("5. Déconnexion");

                    System.out.print("Choisissez une option : ");
                    choix = sc.nextInt();
                    sc.nextLine(); // important

                    switch (choix) {

                        case 1:
                            EleveDAO.afficherEleves();
                            break;

                        case 2:
                            NoteDAO.afficherNotes();
                            break;

                        case 3:
                            AbsenceDAO.afficherAbsences();
                            break;

                        case 4:
                            NotificationDAO.envoyerNotificationsAutomatiques();
                            break;

                        case 5:
                            System.out.println("Déconnexion...");
                            break;

                        default:
                            System.out.println("Choix invalide");
                    }
                }

            } else {
                System.out.println("Email ou mot de passe incorrect ❌");
            }
        }
    }
}
