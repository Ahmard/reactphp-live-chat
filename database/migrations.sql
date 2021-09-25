DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `note_categories`;
DROP TABLE IF EXISTS `notes`;
DROP TABLE IF EXISTS `list_categories`;
DROP TABLE IF EXISTS `lists`;

CREATE TABLE IF NOT EXISTS users
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    username   VARCHAR(30)  NOT NULL,
    email      VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(100) NOT NULL,
    token      VARCHAR(500) NULL,
    type       VARCHAR(15)  NOT NULL DEFAULT 'user',
    created_at TIMESTAMP             DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS messages
(
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    sender_id   INTEGER      NOT NULL,
    receiver_id INTEGER      NOT NULL,
    message     TEXT         NOT NULL,
    conversers  VARCHAR(100) NOT NULL,
    status      INTEGER(1) DEFAULT 0,
    created_at  TIMESTAMP  DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS note_categories
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id    INTEGER      NOT NULL,
    name       VARCHAR(250) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS notes
(
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id     INTEGER      NOT NULL,
    category_id INTEGER      NOT NULL,
    title       VARCHAR(250) NOT NULL,
    note        TEXT         NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS list_categories
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id    INTEGER      NOT NULL,
    name       VARCHAR(250) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS lists
(
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id     INTEGER NOT NULL,
    category_id INTEGER NOT NULL,
    content     TEXT    NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);