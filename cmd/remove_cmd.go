package cmd

import (
	"fmt"

	"github.com/charmbracelet/huh"
	"github.com/spf13/cobra"
)

var removeCmd = &cobra.Command{
	Use:   "remove <server>",
	Short: "Remove a server",
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

		var confirm bool = false

		form := huh.NewForm(
			huh.NewGroup(
				huh.NewConfirm().
					Title("Are you sure you would like to remove this server?").
					Value(&confirm),
			),
		)

		err = form.Run()
		if err != nil {
			fmt.Println(err)
		}

		if !confirm {
			return
		}

		db.Delete(&server)
	},
}

func init() {
	rootCmd.AddCommand(removeCmd)
}
