package cmd

import (
	"fmt"
	"os"
	"strconv"

	"github.com/manifoldco/promptui"
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

		namePrompt := promptui.Prompt{
			Label: "Name",
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
			Label: "Address",
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
			Label: "User",
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
			Default: "22",
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

		userHomeDir, err := os.UserHomeDir()
		if err != nil {
			fmt.Println(err)
			return
		}

		identityFilePrompt := promptui.Prompt{
			Label:   "Identity File",
			Default: userHomeDir + "/.ssh/id_rsa",
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
