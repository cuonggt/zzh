package cmd

import (
	"fmt"
	"os"
	"os/exec"
	"time"
)

type Server struct {
	ID        uint
	Name      string
	Address   string
	User      string
	CreatedAt time.Time
	UpdatedAt time.Time
}

func (s *Server) connectWithSSH() error {
	ssh := exec.Command("ssh", fmt.Sprintf("%s@%s", s.User, s.Address))

	ssh.Stdin = os.Stdin
	ssh.Stderr = os.Stderr
	ssh.Stdout = os.Stdout

	return ssh.Run()
}
