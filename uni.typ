#let uni_style(doc) = {
  set page(
    paper: "a4",
    margin: (top: 2cm, bottom: 2cm, left: 2.5cm, right: 1.5cm),
  )

  set text(
    lang: "ru",
    size: 14pt,
    font: "Times New Roman"
  )

  set heading(
    numbering: "1.",
  )

  set par(
    first-line-indent: (amount: 1.25cm, all: true),
    spacing: 1em,
    leading: 1em,
    justify: true,
  )

  set list(
    marker: [--],
    indent: 1.25cm,
  )

  set enum(
    numbering: "1)",
    indent: 1.25cm,
  )

  show heading: it => {
    let (above, below) = if it.level == 1 {
      (2em, 1.25em)
    } else {
      (2em, 1em)
    }
    return block(
      above: above,
      below: below,
      pad(
        left: 1.25cm,
        text(
          weight: "bold",
          size: 14pt,
          it
        )
      )
    )
  }

  show figure.caption: it => {
    let fig-num = context counter(figure).display()
    [Рисунок #fig-num #it.body]
  }

  doc
}

#let mod(a, b) = int(calc.floor((a - (b * int(calc.floor(a / b))))))

#let my_name = [Баймурадов Т. Р.]
#let my_group = [ИС/б-23-1-о]

#let ТПО = [Тестирование программного обеспечения]
#let ТСИС = [Технические средства информационных систем]
#let ВЕБ = [Веб-технологии]
#let БД = [Управление данными]

#let front_page(
  lab_number: 1,
  lab_name: [],
  subject: [],
  student_name: my_name,
  student_group: my_group,
  prof_info: [],
  prof_name: none,
) = {

  set par(justify: false)

  align(center)[
    #text(size: 12pt)[
      МИНИСТЕРСТВО НАУКИ И ВЫСШЕГО ОБРАЗОВАНИЯ РОССИЙСКОЙ ФЕДЕРАЦИИ \
      ФГАОУ ВО «СЕВАСТОПОЛЬСКИЙ ГОСУДАРСТВЕННЫЙ УНИВЕРСИТЕТ»
    ]
  ]

  v(6em)

  align(right)[Институт информационных технологий]

  v(4em)

  align(center)[
    #block(width: 60%)[
      ОТЧЕТ \
      о выполнении лабораторной работы №#lab_number \
      «#lab_name» \
      по дисциплине \
      «#subject»
    ]
  ]

  v(4em)

  [
    #set par(first-line-indent: (amount: 7.75cm, all: true), hanging-indent: 7.75cm)
    Выполнил:#parbreak()
    ст.гр. #student_group#parbreak()
    #student_name#parbreak()
    #if prof_name != none [
      #v(1em)
      Проверил:#parbreak()
      #prof_info#parbreak()
      #prof_name#parbreak()
    ]
  ]

  place(bottom+center)[
    #align(center)[
      Севастополь, #datetime.today().year()
    ]
  ]

  pagebreak()
}

#let listing_code(body: none, caption: none) = {
  let listing_counter = counter("listing")
  listing_counter.step()
  let current_number = context listing_counter.display()

  [
    Листинг #current_number -- #caption
    #parbreak()
    #text(
      font: "Linux Libertine Mono O",
      size: 10pt,
      [
        #par(
          first-line-indent: (amount: 1.25cm, all: true),
          hanging-indent: 1.25cm,
          leading: 0.5em,
          body
        )
      ]
    )
  ]
}

#let listing_code_file(path, caption: none) = {
  let filename = path.split("/").last()
  caption = if caption == none [Файл #filename] else [#caption]
  let code = raw(read(path))
  listing_code(body: text(code), caption: caption)
}

#let figure_img(path, caption: none, width: 100%) = {
  figure(
    caption: if caption != none [ -- #caption] else [],
    image(path, width: width),
  )
}

#let attachments(..args) = {
  let attachments_counter = counter("attachments")
  set heading(
    numbering: none,
  )

  let num(n) = {
    let letters = ("А", "Б", "В", "Г", "Д", "Е")
    return if n <= 6 { letters.at(n - 1) } else { num(calc.ceil(n / 6) - 1) + num(mod(n, 6)) }
  }

  attachments_counter.step()
  let attachments_number = context attachments_counter.display(num)

  align(center)[= ПРИЛОЖЕНИЕ #attachments_number]
  v(2.5em)

  let image_types = ( "png", "jpg", "jpeg", "gif", "bmp", "svg", "webp", "tiff" )

  let items = args.pos()

  for item in items {
    let (path, caption) = if type(item) == type("") {
      (item, none)
    } else {
      (item.at(0), item.at(1))
    }
    let type = path.split(".").last()
    if type in image_types {
      figure_img(path, caption: caption)
    } else {
      listing_code_file(path, caption: caption)
    }
    v(2em)
  }
}

#let simple_header(number: 1, lab_name: none) = {
  counter(heading).update(number - 1)
  align(
    center,
  )[
    #block(
      below: 1em,
      heading(
        numbering: (..nums) => [],
        [ЛАБОРТОРНАЯ РАБОТА №#number],
      )
    )
    «#lab_name»
  ]
}
