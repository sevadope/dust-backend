<?php

namespace App\Services;

use App\Contracts\JwtInterface;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use Ramsey\Uuid\Uuid;

class JWT implements JwtInterface
{	
	public const OWNER_ID = 'sub';

	protected $signer_alg = Sha256::class;



	public function create($user_id, int $expires_in, array $claims = [])
	{
		$signer = new $this->signer_alg;
		$private_key = new Key(config('jwt.private_key'));

		$builder = $this->configureBuilder($expires_in);

		$builder->withClaim(static::OWNER_ID, $user_id);

		foreach ($claims as $key => $value) {
			$builder->withClaim($key, $value);
		}
		
		$token = $builder->getToken($signer, $private_key);
	
		return $token;
	}

	public function parse(string $token)
	{
		try {
			$token = (new Parser())->parse($token);
			
		} catch (\Exception $e) {

			throw new \App\Exceptions\Api\InvalidJwtException;
		}

		return $token;
	}

	public function verify(Token $token)
	{
		return $token->verify(new $this->signer_alg, config('jwt.public_key'));
	}

	public function verifyRaw(string $token)
	{
		return $this->verify($this->parse($token));
	}

	public function validate(Token $token)
	{
		$data = $this->configureValidationData();

		return $token->validate($data);
	}

	public function getOwnerKey(Token $token)
	{
		return $token->getClaim(static::OWNER_ID);
	}

	public function getOwnerKeybyRaw(string $token)
	{
		return $this->getOwnerKey($this->parse($token));
	}

	protected function configureBuilder($expires_in)
	{
		$time = time();

		return (new Builder)
			->issuedBy(config('app.url'))
			->permittedFor(config('app.url'))
			->identifiedBy($this->generateUuid(), true)
			->issuedAt($time)
			->canOnlyBeUsedAfter($time)
			->expiresAt($time + $expires_in);

	}

	protected function configureValidationData()
	{
		$data = new ValidationData;

		$data->setIssuer(config('app.url'));
		$data->setAudience(config('app.url'));

		return $data;
	}

	protected function generateUuid()
	{
		return (string) Uuid::uuid4();
	}
	
	public static function __callStatic($name, $arguments)
	{
		return (new static)->{$name}($arguments);
	}
}
