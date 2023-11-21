package cmd

import (
	"fmt"
	"os"
	"os/exec"
	"strconv"
	"time"
)

type Server struct {
	ID           uint
	Name         string
	Address      string
	User         string
	Port         uint
	IdentityFile string
	CreatedAt    time.Time
	UpdatedAt    time.Time
}

func (s *Server) ConnectWithSSH() error {
	ssh := exec.Command("ssh", fmt.Sprintf("%s@%s", s.User, s.Address), "-p", strconv.Itoa(int(s.Port)), "-i", s.IdentityFile)

	ssh.Stdin = os.Stdin
	ssh.Stderr = os.Stderr
	ssh.Stdout = os.Stdout

	return ssh.Run()
}
