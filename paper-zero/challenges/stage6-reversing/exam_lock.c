   #include <stdio.h>
   #include <string.h>
   int main(int argc, char **argv) {
       const char *master_key = "unlock_paper_zero_2026";
       const char *flag = "LST{b1n4ry_4n4lys1s_m4st3r}";
       if (argc < 2) { printf("Usage: %s <master_key>\n", argv[0]); return 1; }
       if (strcmp(argv[1], master_key) == 0)
           printf("Vault unlocked. %s\n", flag);
       else
           printf("Access denied.\n");
       return 0;
   }
