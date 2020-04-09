USE NichGall79;

CREATE TABLE author(
    authorId BINARY(36) NOT NULL,
    authorActivationToken CHAR (32) ,
    authorAvatarUrl VARCHAR (255),
    authorEmail VARCHAR(128) NOT NULL,
    authorHash CHAR(97) NOT NULL,
    authorUsername VARCHAR (32) NOT NULL,
    INDEX (authorUsername,authorId),
    UNIQUE(authorEmail, authorUsername),
    PRIMARY KEY (authorId)

);