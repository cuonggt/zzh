package cmd

import (
	"fmt"
	"os"
	"strconv"

	"github.com/charmbracelet/huh"
	"github.com/spf13/cobra"
)

var addCmd = &cobra.Command{
	Use:   "add",
	Short: "Add a server",
	Run: func(cmd *cobra.Command, args []string) {
		db, err := InitDB()
		if err != nil {
			fmt.Println(err)
			return
		}

		userHomeDir, err := os.UserHomeDir()
		if err != nil {
			fmt.Println(err)
			return
		}

		var (
			name         string
			address      string
			user         string
			portString   string = "22"
			identityFile string = userHomeDir + "/.ssh/id_rsa"
		)

		form := huh.NewForm(
			huh.NewGroup(
				huh.NewInput().
					Title("Address").
					Placeholder("IP or Hostname").
					Value(&address).
					Validate(func(s string) error {
						if len(s) == 0 {
							return fmt.Errorf("the address is required")
						}
						return nil
					}),
				huh.NewInput().
					Title("Label").
					Placeholder("Label").
					Value(&name).
					Validate(func(s string) error {
						if len(s) == 0 {
							return fmt.Errorf("the label is required")
						}
						return nil
					}),
				huh.NewInput().
					Title("Username").
					Placeholder("Username").
					Value(&user).
					Validate(func(s string) error {
						if len(s) == 0 {
							return fmt.Errorf("the username is required")
						}
						return nil
					}),
				huh.NewInput().
					Title("Port").
					Placeholder("22").
					Value(&portString).
					Validate(func(s string) error {
						if len(s) == 0 {
							return fmt.Errorf("the port is required")
						}
						_, err := strconv.Atoi(s)
						if err != nil {
							return fmt.Errorf("the port must be integer")
						}
						return nil
					}),
				huh.NewInput().
					Title("Identity File").
					Placeholder("Identity File").
					Value(&identityFile).
					Validate(func(s string) error {
						if len(s) == 0 {
							return fmt.Errorf("the identity file is required")
						}
						return nil
					}),
			),
		)

		err = form.Run()
		if err != nil {
			fmt.Println(err)
		}

		port, err := strconv.Atoi(portString)
		if err != nil {
			fmt.Println(err)
			return
		}

		server := Server{
			Name:         name,
			Address:      address,
			User:         user,
			Port:         uint(port),
			IdentityFile: identityFile,
		}

		result := db.Create(&server)
		if result.Error != nil {
			fmt.Println(result.Error)
			return
		}
	},
}

func init() {
	rootCmd.AddCommand(addCmd)
}
