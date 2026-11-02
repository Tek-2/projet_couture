<?php 

namespace App\State; 

use ApiPlatform\Metadata\Operation; 
use ApiPlatform\State\ProcessorInterface; 
use App\Entity\CommandeLocation; 
use Symfony\Bundle\SecurityBundle\Security; 
use Symfony\Component\Security\Core\Exception\AccessDeniedException; 
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CommandeLocationProcessor implements ProcessorInterface 
{ 
    public function __construct( 
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor, 
        private Security $security ) { } 
        
    public function process(
        mixed $data, Operation $operation, 
        array $uriVariables = [], 
        array $context = []
    ): mixed { 
        if ($data instanceof CommandeLocation) { 
            $user = $this->security->getUser(); 
            if (!$user) { 
                throw new AccessDeniedException('Vous devez être connectée pour créer une réservation.'); 
            } 
            $data->setUser($user); 
        } 
        return $this->persistProcessor->process(
        $data, $operation, $uriVariables, $context); 
    } 
}