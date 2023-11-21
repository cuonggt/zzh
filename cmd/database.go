package cmd

import (
	"os"

	"gorm.io/driver/sqlite"
	"gorm.io/gorm"
)

func InitDB() (*gorm.DB, error) {
	userConfigDir, err := os.UserConfigDir()
	if err != nil {
		return nil, err
	}

	path := userConfigDir + "/zzh"

	err = os.MkdirAll(path, os.ModePerm)
	if err != nil {
		return nil, err
	}

	db, err := gorm.Open(sqlite.Open("file:"+path+"/zzh.db?cache=shared"), &gorm.Config{})
	if err != nil {
		return nil, err
	}

	if err := db.AutoMigrate(&Server{}); err != nil {
		return nil, err
	}

	return db, nil
}
