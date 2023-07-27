package cmd

import (
	"fmt"

	"github.com/spf13/cobra"
)

var listCmd = &cobra.Command{
	Use: "list",
	Run: func(cmd *cobra.Command, args []string) {
		fmt.Println("list")
	},
}

func init() {
	rootCmd.AddCommand(listCmd)
}
