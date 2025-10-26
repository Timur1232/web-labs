package main

import (
	"github.com/labstack/echo/v4/middleware"
	"github.com/labstack/echo/v4"
)

func main() {
	e := echo.New()
	e.Use(middleware.Logger())

	e.Static("/styles", "styles")
	e.Static("/media", "media")
	e.Static("/fonts", "fonts")
	e.Static("/js", "js")

	e.GET("/", index)
	e.GET("/about_me", about_me)
	e.GET("/my_interests", my_interests)
	e.GET("/studies", studies)
	e.GET("/studies/test", test)
	e.GET("/photoalbum", photoalbum)
	e.GET("/callback", callback)
	e.GET("/not_exist", not_exist)
	e.GET("/history", history)

	e.Logger.Fatal(e.Start(":42069"))
}

func index(c echo.Context) error {
	return c.File("views/index.html")
}

func about_me(c echo.Context) error {
	return c.File("views/about_me.html")
}

func my_interests(c echo.Context) error {
	return c.File("views/my_interests.html")
}

func studies(c echo.Context) error {
	return c.File("views/studies.html")
}

func test(c echo.Context) error {
	return c.File("views/test.html")
}

func photoalbum(c echo.Context) error {
	return c.File("views/photoalbum.html")
}

func callback(c echo.Context) error {
	return c.File("views/callback.html")
}

func not_exist(c echo.Context) error {
	return c.File("views/not_exist.html")
}

func history(c echo.Context) error {
	return c.File("views/history.html")
}
