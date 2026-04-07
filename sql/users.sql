create table if not exists users (
    fio varchar(50) not null,
    email varchar(50) not null,
    login varchar(50) not null,
    password_hash varchar(100) not null
)
