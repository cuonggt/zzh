package cmd

import (
	"gorm.io/driver/sqlite"
	"gorm.io/gorm"
)

func InitDB() (*gorm.DB, error) {
	// fmt.Println(os.UserConfigDir())
	db, err := gorm.Open(sqlite.Open("file:zzh.db?cache=shared"), &gorm.Config{})
	if err != nil {
		return nil, err
	}

	if err := db.AutoMigrate(&Server{}); err != nil {
		return nil, err
	}

	return db, nil
}
