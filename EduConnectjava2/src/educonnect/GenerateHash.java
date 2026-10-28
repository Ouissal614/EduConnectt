package educonnect;

import org.mindrot.jbcrypt.BCrypt;

public class GenerateHash {
    public static void main(String[] args) {

        String password = "1234";

        String hash = BCrypt.hashpw(password, BCrypt.gensalt());

        System.out.println("Hash généré : " + hash);
    }
}