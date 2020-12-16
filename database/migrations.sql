CREATE TABLE IF NOT EXISTS users
(
    id       INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(30)  NOT NULL,
    email    VARCHAR(50)  NOT NULL,
    password VARCHAR(100) NOT NULL,
    token    VARCHAR(500) NULL,
    type     VARCHAR(15)  NOT NULL DEFAULT 'user',
    time     INTEGER(15)  NOT NULL
);


CREATE TABLE IF NOT EXISTS messages
(
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    sender_id   INTEGER      NOT NULL,
    receiver_id INTEGER      NOT NULL,
    message     TEXT         NOT NULL,
    conversers  VARCHAR(100) NOT NULL,
    status      INTEGER(1) DEFAULT 0,
    time        INTEGER(15)  NOT NULL
);


CREATE TABLE IF NOT EXISTS notes
(
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id     INTEGER      NOT NULL,
    category_id INTEGER      NOT NULL,
    title       VARCHAR(250) NOT NULL,
    note        TEXT         NOT NULL,
    created_at  VARCHAR(30)  NOT NULL,
    updated_at  VARCHAR(30)  NOT NULL
);


CREATE TABLE IF NOT EXISTS categories
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id     INTEGER      NOT NULL,
    name       VARCHAR(250) NOT NULL,
    created_at VARCHAR(30)  NOT NULL,
    updated_at VARCHAR(30)  NOT NULL
);