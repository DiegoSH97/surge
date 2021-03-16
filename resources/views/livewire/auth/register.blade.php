<form wire:submit.prevent="register">
    <div>
        <label for="name">Name</label>
        <input wire:model="name" type="text" id="name" name="name">
        @error('name') <span> {{ $message }} </span> @enderror
    </div>
    <div>
        <label for="email">Email</label>
        <input wire:model="email" type="text" id="email" name="emai">
        @error('email') <span> {{ $message }} </span> @enderror
    </div>
    <div>
        <label for="password">Password</label>
        <input wire:model="password" type="password" id="password" name="password">
        @error('password') <span> {{ $message }} </span> @enderror
    </div>

     <div>
        <label for="passwordConfirmation">Password Confirmation</label>
        <input wire:model="passwordConfirmation" type="password" id="passwordConfirmation" name="passwordConfirmation">
     </div>

     <div>
         <input type="submit" value="register">
     </div>
</form>
