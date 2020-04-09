<?php
namespace NichGall79\ObjectOriented;

require_once("autoload.php");
require_once(dirname(__DIR__) . "/vendor/autoload.php");


use http\Encoding\Stream;
use Ramsey\Uuid\Uuid;
/*
This is a class made for registering books in a library or book stored
@author Nicholas Gallegos <ngallegos61@cnm.edu>
*/


class Author implements \JsonSerializable  {
	use ValidateUuid;

	/*
	*/

	private $authorId;

	/*
	*/

	private $authorAvatarUrl;

	/*
	*/

	private $authorActivationToken;

	/*
	*/

	private $authorEmail;

	/*
	*/

	private $authorHash;

	/*
	*/

	private $authorUsername;

	/*
	 * Making constructors
	 *
	 */
	public function __construct($newAuthorId, ?string $newAuthorActivationToken,string $newAuthorAvatarUrl,string $newAuthorEmail, string $newAuthorHash, string $newAuthorUsername) {
		try {
			$this->setAuthorId($newAuthorId);
			$this->setAuthorActivationToken($newAuthorActivationToken);
			$this->setAuthorUsername($newAuthorUsername);
			$this->setAuthorAvatarUrl($newAuthorAvatarUrl);
			$this->setAuthorEmail($newAuthorEmail);
			$this->setAuthorHash($newAuthorHash);
		} catch(\InvalidArgumentException | \RangeException |\TypeError | \Exception $exception) {
			//determine what exception type was thrown
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}

	/*Accessor for Author Id */

	public function getAuthorId(): Uuid {
		return ($this->authorId);
	}

// Mutator for Author Id
	public function setAuthorId($newAuthorId): void {

		//verify the author id is valid
		try {
			$uuid = self::validateUuid($newAuthorId);

		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
		$this->authorId= $uuid;
		echo "$uuid";

	}

	/*
	 * Accessor for author avatar url
	 *  */

	public function getAuthorAvatarUrl(): string {
		return ($this->authorAvatarUrl);
	}

	// Mutator for author avatar url
	public function setAuthorAvatarUrl(string $newAuthorAvatarUrl) : void {
		// Making sure there are no whitespaces
		$newAuthorAvatarUrl = trim($newAuthorAvatarUrl);
		$newAuthorAvatarUrl = filter_var($newAuthorAvatarUrl, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		// verify the avatar URL will fit in the database
		if(strlen($newAuthorAvatarUrl) > 255) {
			throw(new \RangeException("image content too large"));
		}
		// store the image cloudinary content
		$this->authorAvatarUrl = $newAuthorAvatarUrl;
	}

	/* Accessor for Author activation token */

	public function getAuthorActivationToken(): ?string {
		return ($this->authorActivationToken);
	}

	// Mutator for author activation token
	public function setAuthorActivationToken(?string $newAuthorActivationToken): void {
		//Verifying field is not empty
		if($newAuthorActivationToken===null){
			throw (new \InvalidArgumentException("Not token"));
		}
		//Making sure the input matches the database character length
		if (strlen($newAuthorActivationToken)!==32){
			throw (new \RangeException("Must be 32 characters"));
		}

		$this->authorActivationToken = $newAuthorActivationToken;
	}

	/*Accessor for Author Email*/

	public function getAuthorEmail(): string {
		return $this->authorEmail;
	}

	// Mutator for Author email
	public function setAuthorEmail(string $newAuthorEmail): void {

		// verify the email is secure
		$newAuthorEmail = trim($newAuthorEmail);
		$newAuthorEmail = filter_var($newAuthorEmail, FILTER_VALIDATE_EMAIL,FILTER_FLAG_EMAIL_UNICODE);
// I
		if(empty($newAuthorEmail) === true) {
			throw (new \InvalidArgumentException("Author email is empty or insecure"));
		}

		// verify the email will fit in the database
		if(strlen($newAuthorEmail) > 128) {
			throw (new \RangeException("Author email is too large"));
		}

		// store the email
		$this->authorEmail = $newAuthorEmail;
	}

	/*Accessor for Author hash from password conversion */

	public function getAuthorHash(): string {
		return ($this->authorHash);
	}

	// Mutator for Author hash
	public function setAuthorHash($newAuthorHash): void {
		//enforce that the hash is properly formatted
		$newAuthorHash = trim($newAuthorHash);
		if(empty($newAuthorHash) === true) {
			throw (new \InvalidArgumentException("Not a valid hash"));
		}


		//enforce the hash is really an Argon hash
		$authorHashInfo = password_get_info($newAuthorHash);
		if($authorHashInfo["algoName"] !== "argon2i") {
			throw(new \InvalidArgumentException("profile hash is not a valid hash"));
		}

		//enforce that the hash is exactly 97 characters.
		if(strlen($newAuthorHash)>97){
			throw (new \RangeException("Must be 97 character"));
		}

		//store the hash
		$this->authorHash = $newAuthorHash;
	}

	/*Accessor for authorUsername */

	public function getAuthorUsername(): string {
		return ($this->authorUsername);
	}


	// Mutator for Author Username
	public function setAuthorUsername(string $newAuthorUsername): void {
		$newAuthorUsername = trim($newAuthorUsername);
		$newAuthorUsername = filter_var($newAuthorUsername, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(strlen($newAuthorUsername) > 32) {
			throw (new \RangeException("Username is too long"));
		}
		if(empty($newAuthorUsername) === true) {
			throw (new \InvalidArgumentException("Not a secure username or it is empty"));
		}
		//store the username
		$this->authorUsername = $newAuthorUsername;
	}
	/**
	 * inserts into mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/


	public function insert(\PDO $pdo) : void  {


		// create query template
		$query = "INSERT INTO author(authorId,authorActivationToken, authorAvatarUrl, authorEmail, authorHash,authorUsername) 
						VALUES(:authorId,:authorActivationToken, :authorAvatarUrl, :authorEmail, :authorHash, :authorUsername)";
		$statement = $pdo->prepare($query);

		//binding table attributes to placeholders
		$parameters = ["authorId"=> $this->getAuthorId()->getBytes(),"authorActivationToken"=>$this->authorActivationToken,"authorAvatarUrl"=>$this->authorAvatarUrl,"authorEmail"=>$this->authorEmail,
			"authorHash"=>$this->authorHash, "authorUsername"=>$this->authorUsername];
		$statement->execute($parameters);
	}


	/**
	 * deletes this attributes from mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function delete(\PDO $pdo) : void {

		// create query template
		$query = "DELETE FROM author WHERE authorId = :authorId";
		$statement = $pdo->prepare($query);

		$parameters = ["authorId" =>$this->getAuthorId()->getBytes()];
		$statement ->execute($parameters);
	}

	/**
	 * updates author into SQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function update(\PDO $pdo) : void {

		// create query template
		$query = "UPDATE author SET authorId  = :authorId,
 	authorActivationToken =:authorActivationToken,
 	authorAvatarUrl  = :authorAvatarUrl, 
 	authorEmail = :authorEmail,
  	authorHash = :authorHash, 
	authorUsername = :authorUsername
 					WHERE authorId = :authorId";

		$statement = $pdo->prepare($query);


//binds class objects to sql placeholders
		$parameters = ["authorId"=> $this->getAuthorId()->getBytes(),
			"authorActivationToken"=>$this->authorActivationToken,
			"authorAvatarUrl"=>$this->authorAvatarUrl,
			"authorEmail"=>$this->authorEmail,
			"authorHash"=>$this->authorHash,
			"authorUsername"=>$this->authorUsername];

		$statement->execute($parameters);
	}



	public function getAllAuthor(\PDO $pdo) {
		// create query template
		$query= "SELECT * FROM author";
		$statement = $pdo->prepare($query);
		$statement->execute();

		// build an array of author
		$authors = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$author = new Author($row["authorId"],$row["authorActivationToken"],$row["authorAvatarUrl"],$row["authorEmail"],$row["authorHash"],$row["authorUsername"]);
				//To know the length of an array when you have no clue what's in it
				$authors[$authors->key()] = $author;
				$authors->next();
			} catch(\Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}

		return ($authors);

		//Fetch all authors from database
		//$row = $statement->fetchAll();

		//returned Array of author
		//return $row;

	}


	public function getAuthor(\PDO $pdo, $authorId): ?Author {
		//create query template
		$query = "SELECT authorId,
		authorActivationToken,
		authorAvatarUrl,
		authorEmail,
		authorHash,
		authorUsername 
		FROM author WHERE authorId = :authorId";
		$statement = $pdo->prepare($query);
		try {
			$authorId = self::validateUuid($authorId);
		} catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}

		//bind the objects to their respective placeholders in the table
		$parameters = ["authorId" => $authorId->getBytes()];
		$statement->execute($parameters);

		//grab author from database

		$author = null;
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		$row = $statement->fetch();
		if($row !== false){
			//instantiate author object and push data into it
			$author = new Author($row["authorId"],$rowT["authorActivationToken"],$row["authorAvatarUrl"],$row["authorEmail"],$row["authorHash"],$row["authorUsername"]);
		}
		//var_dump($author);
		return ($author);


	}




	public function jsonSerialize() : array {
		$fields = get_object_vars($this);

		$fields["authorId"] = $this->authorId->toString();
		return($fields);
	}

}
