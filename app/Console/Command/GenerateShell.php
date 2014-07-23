<?php
App::uses( 'Shell', 'Console' );
App::uses( 'File', 'Utility' );

/**
* GenerateShell
*
* @uses AppShell
*
* @author Florent Sorel (Rtransat)
*/
class GenerateShell extends AppShell {

	public function main() {
		$this->salt();
		$this->seed();
	}

    /**
     * salt
     * 
     * @access public
     *
     * @return void
     */
	public function salt() {
		$file = new File( APP . 'Config/core.php' );
		if ( $file->exists() ) {
			$contents = $file->read();
			$salt = $this->_generateSalt();

			$contents = preg_replace( '/(?<=Configure::write\(\'Security.salt\', \')([^\' ]+)(?=\'\))/', $salt, $contents );

			if ( !$file->write( $contents ) )
				return false;

			$this->out( "The security salt has been changed" );
		}
		else {
			$this->out( "{$file->name} doesn't exist" );
		}
	}

    /**
     * seed
     * 
     * @access public
     *
     * @return void
     */
	public function seed() {
		$file = new File( APP . 'Config/core.php' );
		if ( $file->exists() ) {
			$contents = $file->read();
			$seed = $this->_generateRandomNumericString();

			$contents = preg_replace( '/(?<=Configure::write\(\'Security.cipherSeed\', \')(\d+)(?=\'\))/', $seed, $contents );

			if ( !$file->write( $contents ) )
				return false;

			$this->out( "The security seed has been changed" );
		}
		else {
			$this->out( "{$file->name} doesn't exist" );
		}
	}

    /**
     * _generateSalt
     * 
     * @param int $length Length of string.
     *
     * @access private
     *
     * @return string
     */
	private function _generateSalt( $length = 40 ) {
		$salt = str_replace(
			array( '+', '=' ),
			'.',
			base64_encode( sha1( uniqid( Configure::read( 'Security.salt' ), true ), false ) )
		);
		return substr( $salt, 0, $length );
	}

    /**
     * _generateRandomNumericString
     * 
     * @param int $length Length of string.
     *
     * @access private
     *
     * @return string
     */
	private function _generateRandomNumericString( $length = 29 ) {
		$characters = '0123456789';
		$string = '';
		for ( $p = 0; $p < $length; $p++ ) {
			$string .= $characters[mt_rand( 0, strlen( $characters ) - 1 )];
		}
		return $string;
	}

}