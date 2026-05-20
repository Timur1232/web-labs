create table if not exists blogs (
    id integer primary key,
    datestr varchar(15) not null,
    author varchar(50) not null,
    title varchar(50) not null,
    image_path varchar(50) default null,
    text text not null
);

create table if not exists blog_comments (
    id integer primary key,
    user_id integer not null,
    blog_id integer not null,
    datestr varchar(15) not null,
    text text not null,

    foreign key (user_id) references users(id)
        on delete cascade
        on update cascade,
    foreign key (blog_id) references blogs(id)
        on delete cascade
        on update cascade
);
