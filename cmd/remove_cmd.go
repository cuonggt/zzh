package cmd

import (
	"fmt"

	"github.com/manifoldco/promptui"
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

		confirmPrompt := promptui.Prompt{
			Label:     "Remove Server",
			Default:   "n",
			IsConfirm: true,
		}

		confirm, err := confirmPrompt.Run()
		if err != nil {
			fmt.Println(err)
		}

		if confirm != "y" {
			return
		}

		db.Delete(&server)
	},
}

func init() {
	rootCmd.AddCommand(removeCmd)
}
