FROM ubuntu:22.04
RUN apt-get update && apt-get install -y openssh-server && mkdir -p /var/run/sshd

# low-privileged user with the password leaked in Stage 3
RUN useradd -m -s /bin/bash lecturer && echo 'lecturer:LST_2026_Secure!' | chpasswd

# root-only flag
RUN echo 'LST{r00t_4cc3ss_gr4nt3d}' > /root/flag.txt && chmod 600 /root/flag.txt

# breadcrumb to Stage 5; use the same Paper Zero host in the browser
RUN echo 'Admin health dashboard: open /stage5/ on the same host/IP used for Paper Zero.' > /root/next_step.txt

# deliberate misconfiguration: SUID on find (GTFOBins privesc)
RUN chmod u+s /usr/bin/find

# allow password login, disable root login and PAM (container-friendly)
RUN sed -i 's/#*PasswordAuthentication.*/PasswordAuthentication yes/' /etc/ssh/sshd_config \
    && sed -i 's/#*PermitRootLogin.*/PermitRootLogin no/' /etc/ssh/sshd_config \
    && sed -i 's/#*UsePAM.*/UsePAM no/' /etc/ssh/sshd_config

EXPOSE 22
CMD ["/usr/sbin/sshd","-D"]
