package educonnect;

import java.util.Scanner;

public class TestLoginParent {

    public static void main(String[] args) {

        Scanner sc = new Scanner(System.in);

        while (true) {

            System.out.println("\n=== LOGIN PARENT ===");

            System.out.print("Login : ");
            String login = sc.nextLine();

            System.out.print("Mot de passe : ");
            String password = sc.nextLine();

            boolean ok = ParentDAO.login(login, password);

            if (ok) {

                System.out.println("Connexion réussie ✔ Parent connecté");

                int choix = 0;

                while (choix != 4) {

                    System.out.println("\n=== MENU PARENT ===");
                    System.out.println("1. Voir notes de mon enfant");
                    System.out.println("2. Voir absences de mon enfant");
                    System.out.println("3. Voir bulletin");
                    System.out.println("4. Déconnexion");

                    System.out.print("Choisissez une option : ");
                    choix = sc.nextInt();
                    sc.nextLine(); // vider buffer

                    switch (choix) {

                        case 1:
                            ParentDAO.afficherNotes(login);
                            break;

                        case 2:
                            ParentDAO.afficherAbsences(login);
                            break;

                        case 3:
                            ParentDAO.afficherBulletin(login);
                            ParentDAO.afficherMoyenne(login);
                            break;

                        case 4:
                            System.out.println("Déconnexion...");
                            break;

                        default:
                            System.out.println("Choix invalide");
                    }
                }

                // sécurise retour login
                sc.nextLine();

            } else {
                System.out.println("Login ou mot de passe incorrect ❌");
            }
        }
    }
}