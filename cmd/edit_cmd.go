package cmd

import (
	"fmt"
	"strconv"

	"github.com/charmbracelet/huh"
	"github.com/spf13/cobra"
)

var editCmd = &cobra.Command{
	Use:   "edit <server>",
	Short: "Edit a server",
	Args:  cobra.ExactArgs(1),
	Run: func(cmd *cobra.Command, args []string) {
		db, err := InitDB()
		if err != nil {
			fmt.Println(err)
			return
		}

		server := Server{}
		result := db.Where("name = ?", args[0]).First(&server)
		if result.Error != nil {
			fmt.Println(result.Error)
			return
		}

		var (
			name         string = server.Name
			address      string = server.Address
			user         string = server.User
			portString   string = strconv.Itoa(int(server.Port))
			identityFile string = server.IdentityFile
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

		result = db.Model(&server).Updates(Server{
			Name:         name,
			Address:      address,
			User:         user,
			Port:         uint(port),
			IdentityFile: identityFile,
		})

		if result.Error != nil {
			fmt.Println(result.Error)
			return
		}
	},
}

func init() {
	rootCmd.AddCommand(editCmd)
}
