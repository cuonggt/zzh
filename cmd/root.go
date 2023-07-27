package cmd

import (
	"fmt"
	"io"
	"os"
	"os/exec"
	"strings"

	"github.com/charmbracelet/bubbles/list"
	tea "github.com/charmbracelet/bubbletea"
	"github.com/charmbracelet/lipgloss"
	"github.com/spf13/cobra"
)

const listHeight = 14

var (
	itemStyle         = lipgloss.NewStyle().PaddingLeft(4)
	selectedItemStyle = lipgloss.NewStyle().PaddingLeft(2).Foreground(lipgloss.Color("170"))
)

type Item struct {
	server Server
}

func (i Item) FilterValue() string {
	return i.server.Name
}

type ItemDelegate struct {
}

func (d *ItemDelegate) Height() int {
	return 1
}

func (d *ItemDelegate) Spacing() int {
	return 0
}

func (d *ItemDelegate) Update(msg tea.Msg, m *list.Model) tea.Cmd {
	return nil
}

func (d *ItemDelegate) Render(w io.Writer, m list.Model, index int, listItem list.Item) {
	i, ok := listItem.(Item)
	if !ok {
		return
	}

	str := fmt.Sprintf("%d. %s", index+1, i.server.Name)

	fn := itemStyle.Render
	if index == m.Index() {
		fn = func(s ...string) string {
			return selectedItemStyle.Render("> " + strings.Join(s, " "))
		}
	}

	fmt.Fprint(w, fn(str))
}

type Selector struct {
	list     list.Model
	selected *Server
}

func (s *Selector) Init() tea.Cmd {
	return nil
}

func (s *Selector) Update(msg tea.Msg) (tea.Model, tea.Cmd) {
	switch msg := msg.(type) {
	case tea.WindowSizeMsg:
		s.list.SetWidth(msg.Width)
		return s, nil
	case tea.KeyMsg:
		switch keypress := msg.String(); keypress {
		case "ctrl+c":
			return s, tea.Quit
		case "enter":
			i, ok := s.list.SelectedItem().(Item)
			if ok {
				s.selected = &i.server
			}
			return s, tea.Quit
		}
	}
	var cmd tea.Cmd
	s.list, cmd = s.list.Update(msg)
	return s, cmd
}

func (s *Selector) View() string {
	return s.list.View()
}

func NewSelector() *Selector {
	items := []list.Item{}
	const defaultWidth = 20

	l := list.New(items, &ItemDelegate{}, defaultWidth, listHeight)
	l.Title = "Which server do you want to connect?"
	l.SetShowStatusBar(false)

	return &Selector{list: l}
}

var rootCmd = &cobra.Command{
	Use:   "zzh",
	Short: "SSH client and connection manager in your favorite terminal.",
	Run: func(cmd *cobra.Command, args []string) {
		s := NewSelector()

		_, err := tea.NewProgram(s).Run()

		if err != nil {
			fmt.Println("Error running program:", err)
			os.Exit(1)
		}

		if s.selected == nil {
			return
		}

		ssh := exec.Command("ssh", fmt.Sprintf("%s@%s", s.selected.User, s.selected.Address))

		ssh.Stdin = os.Stdin
		ssh.Stderr = os.Stderr
		ssh.Stdout = os.Stdout

		if err := ssh.Run(); err != nil {
			fmt.Println(err)
		}
	},
}

func Execute() {
	if err := rootCmd.Execute(); err != nil {
		fmt.Fprintln(os.Stderr, err)
		os.Exit(1)
	}
}
