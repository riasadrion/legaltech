@include('errors.layout', [
    'code'    => 403,
    'title'   => 'Forbidden',
    'message' => $exception?->getMessage() ?: 'You do not have permission to access this page.',
])
