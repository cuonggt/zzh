package cmd

import (
	"fmt"
	"strconv"

	"github.com/manifoldco/promptui"
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

		namePrompt := promptui.Prompt{
			Label:   "Name",
			Default: server.Name,
			Validate: func(s string) error {
				if len(s) == 0 {
					return fmt.Errorf("required")
				}
				return nil
			},
		}

		name, err := namePrompt.Run()
		if err != nil {
			fmt.Println(err)
			return
		}

		addressPrompt := promptui.Prompt{
			Label:   "Address",
			Default: server.Address,
			Validate: func(s string) error {
				if len(s) == 0 {
					return fmt.Errorf("required")
				}
				return nil
			},
		}

		address, err := addressPrompt.Run()
		if err != nil {
			fmt.Println(err)
			return
		}

		userPrompt := promptui.Prompt{
			Label:   "User",
			Default: server.User,
			Validate: func(s string) error {
				if len(s) == 0 {
					return fmt.Errorf("required")
				}
				return nil
			},
		}

		user, err := userPrompt.Run()
		if err != nil {
			fmt.Println(err)
			return
		}

		portPrompt := promptui.Prompt{
			Label:   "Port",
			Default: strconv.Itoa(int(server.Port)),
			Validate: func(s string) error {
				if len(s) == 0 {
					return fmt.Errorf("required")
				}
				_, err := strconv.Atoi(s)
				if err != nil {
					return fmt.Errorf("must be integer")
				}
				return nil
			},
		}

		portString, err := portPrompt.Run()
		if err != nil {
			fmt.Println(err)
			return
		}

		port, err := strconv.Atoi(portString)
		if err != nil {
			fmt.Println(err)
			return
		}

		identityFilePrompt := promptui.Prompt{
			Label:   "Identity File",
			Default: server.IdentityFile,
			Validate: func(s string) error {
				if len(s) == 0 {
					return fmt.Errorf("required")
				}
				return nil
			},
		}

		identityFile, err := identityFilePrompt.Run()
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
